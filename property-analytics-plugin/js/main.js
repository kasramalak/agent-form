jQuery(document).ready(function($) {
    function parseOverview() {
        const address = $('.property-address-wrap .list-lined').text().trim();
        const title = $('.page-title h1').text().trim();
        return { address, title };
    }

    function parseMultipleUnits() {
        const units = [];
        $('.inventory-item').each(function() {
            const table = $(this).find('.multi-units-table');
            const unit = {
                title: table.find('td').eq(0).text().trim(),
                type: table.find('td').eq(1).text().trim(),
                price: table.find('td').eq(2).text().trim(),
                bedrooms: table.find('td').eq(3).text().trim(),
                size: table.find('td').eq(4).text().trim(),
            };
            units.push(unit);
        });
        return units;
    }

    function displayUnitAnalytics(unit, analytics) {
        const container = $('.multiple-units-container');
        if (container.length === 0) {
            // Create the container if it doesn't exist
            $('.property-sub-listings-wrap .block-content-wrap').append('<div class="multiple-units-container"></div>');
        }

        const unitContainer = $('<div class="unit-analytics"></div>');
        unitContainer.html(`
            <h2>Analytics for ${unit.title}</h2>
            <div>
                <h3>Average Prices</h3>
                <p><strong>Sale:</strong> AED ${analytics.averagePrices.sale.toFixed(2)}</p>
                <p><strong>Rent:</strong> AED ${analytics.averagePrices.rent.toFixed(2)}</p>
            </div>
            <div>
                <h3>Recent Sales Transactions</h3>
                <ul>
                    ${analytics.recentTransactions.sales.map(t => `<li>${t.transaction_date}: AED ${t.price_aed}</li>`).join('')}
                </ul>
            </div>
            <div>
                <h3>Recent Rent Transactions</h3>
                <ul>
                    ${analytics.recentTransactions.rents.map(t => `<li>${t.start_date}: AED ${t.price_aed}</li>`).join('')}
                </ul>
            </div>
            <div>
                <h3>Average Price for ${unit.type}</h3>
                <p>AED ${analytics.averagePriceForType.toFixed(2)}</p>
            </div>
            <div>
                <h3>Price per Sq Ft Graph</h3>
                <canvas></canvas>
            </div>
        `);
        container.append(unitContainer);
    }

    function createPricePerSqFtGraph(canvas, valuationData) {
        const sales = valuationData.recentTransactions.sales;
        const monthlyData = {};

        for (const transaction of sales) {
            const date = new Date(transaction.transaction_date);
            const month = date.toLocaleString('default', { month: 'long' });
            const year = date.getFullYear();
            const key = `${month} ${year}`;

            if (!monthlyData[key]) {
                monthlyData[key] = { total: 0, count: 0, average: 0 };
            }

            monthlyData[key].total += transaction.price_aed / transaction.size_sqm;
            monthlyData[key].count++;
        }

        const labels = [];
        const data = [];
        const threeMonthsAgo = new Date();
        threeMonthsAgo.setMonth(threeMonthsAgo.getMonth() - 3);

        for (const key in monthlyData) {
            const [month, year] = key.split(' ');
            const date = new Date(`${month} 1, ${year}`);
            if (date >= threeMonthsAgo) {
                labels.push(key);
                data.push(monthlyData[key].total / monthlyData[key].count);
            }
        }

        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Average Price per Sq Ft (Sale)',
                    data: data,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    async function main() {
        const overview = parseOverview();
        const units = parseMultipleUnits();

        for (const unit of units) {
            const data = {
                action: 'get_property_analytics',
                nonce: property_analytics.nonce,
                unit: unit,
                address: overview.address,
            };

            $.post(property_analytics.ajax_url, data, function(response) {
                if (response.success) {
                    displayUnitAnalytics(unit, response.data);
                    const canvas = $(`.unit-analytics:last`).find('canvas')[0];
                    createPricePerSqFtGraph(canvas, response.data);
                }
            });
        }
    }

    main();
});
