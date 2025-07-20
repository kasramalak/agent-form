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
        `);
        container.append(unitContainer);
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
                }
            });
        }
    }

    main();
});
