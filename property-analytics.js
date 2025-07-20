// This file will contain the functions for fetching and displaying property analytics.

const unirest = require('unirest');

async function getPslCode(address = "JVC, Dubai") {
  const response = await unirest
    .get('https://propsearch.ae/api/vista/smart-match')
    .headers({
      'Authorization': 'Bearer 18|OtD14rmbXTZlPSnMtLlgUBU5hXQmOu44KJvOxb32c1855e61',
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    })
    .query({
      address: address,
    });

  if (response.body.match_status === 'Match found') {
    return response.body.match_psl_code;
  } else {
    return null;
  }
}

async function getValuation(pslCode, segment, sizeSqm, bedrooms) {
  const response = await unirest
    .get('https://propsearch.ae/api/vista/valuations')
    .headers({
      'Authorization': 'Bearer 18|OtD14rmbXTZlPSnMtLlgUBU5hXQmOu44KJvOxb32c1855e61',
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    })
    .query({
      psl_code: pslCode,
      segment: segment,
      size_sqm: sizeSqm,
      bedrooms: bedrooms,
    });

  return response.body;
}

function calculateAveragePrices(valuationData) {
  const sales = valuationData.estimate.sale.comparables.government_transactions;
  const rents = valuationData.estimate.rent.comparables.government_transactions;

  const averagePrices = {
    sale: {
      '1-bed': { total: 0, count: 0, average: 0 },
      '2-bed': { total: 0, count: 0, average: 0 },
      '3-bed': { total: 0, count: 0, average: 0 },
    },
    rent: {
      '1-bed': { total: 0, count: 0, average: 0 },
      '2-bed': { total: 0, count: 0, average: 0 },
      '3-bed': { total: 0, count: 0, average: 0 },
    },
  };

  for (const transaction of sales) {
    if (averagePrices.sale[transaction.room_type]) {
      averagePrices.sale[transaction.room_type].total += transaction.price_aed;
      averagePrices.sale[transaction.room_type].count++;
    }
  }

  for (const transaction of rents) {
    if (averagePrices.rent[transaction.room_type]) {
      averagePrices.rent[transaction.room_type].total += transaction.price_aed;
      averagePrices.rent[transaction.room_type].count++;
    }
  }

  for (const type in averagePrices.sale) {
    if (averagePrices.sale[type].count > 0) {
      averagePrices.sale[type].average = averagePrices.sale[type].total / averagePrices.sale[type].count;
    }
  }

  for (const type in averagePrices.rent) {
    if (averagePrices.rent[type].count > 0) {
      averagePrices.rent[type].average = averagePrices.rent[type].total / averagePrices.rent[type].count;
    }
  }

  return averagePrices;
}

function displayAveragePrices(averagePrices) {
  document.getElementById('avg-sale-1-bed').textContent = `AED ${averagePrices.sale['1-bed'].average.toFixed(2)}`;
  document.getElementById('avg-sale-2-bed').textContent = `AED ${averagePrices.sale['2-bed'].average.toFixed(2)}`;
  document.getElementById('avg-sale-3-bed').textContent = `AED ${averagePrices.sale['3-bed'].average.toFixed(2)}`;

  document.getElementById('avg-rent-1-bed').textContent = `AED ${averagePrices.rent['1-bed'].average.toFixed(2)}`;
  document.getElementById('avg-rent-2-bed').textContent = `AED ${averagePrices.rent['2-bed'].average.toFixed(2)}`;
  document.getElementById('avg-rent-3-bed').textContent = `AED ${averagePrices.rent['3-bed'].average.toFixed(2)}`;
}

function displayRecentTransactions(valuationData) {
  const sales = valuationData.estimate.sale.comparables.government_transactions.slice(0, 4);
  const rents = valuationData.estimate.rent.comparables.government_transactions.slice(0, 4);

  const salesContainer = document.getElementById('recent-sales');
  const rentsContainer = document.getElementById('recent-rents');

  for (const transaction of sales) {
    const transactionElement = document.createElement('div');
    transactionElement.innerHTML = `
      <p><strong>Date:</strong> ${transaction.transaction_date}</p>
      <p><strong>Price:</strong> AED ${transaction.price_aed}</p>
      <p><strong>Type:</strong> ${transaction.room_type}</p>
      <p><strong>Size:</strong> ${transaction.size_sqm} sqm</p>
    `;
    salesContainer.appendChild(transactionElement);
  }

  for (const transaction of rents) {
    const transactionElement = document.createElement('div');
    transactionElement.innerHTML = `
      <p><strong>Date:</strong> ${transaction.start_date}</p>
      <p><strong>Price:</strong> AED ${transaction.price_aed}</p>
      <p><strong>Type:</strong> ${transaction.room_type}</p>
      <p><strong>Size:</strong> ${transaction.size_sqm} sqm</p>
    `;
    rentsContainer.appendChild(transactionElement);
  }
}

function createPricePerSqFtGraph(valuationData) {
  const sales = valuationData.estimate.sale.comparables.government_transactions;
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

  const ctx = document.getElementById('price-per-sq-ft-chart').getContext('2d');
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

async function main(html) {
  const overview = parseOverview(html);
  const units = parseMultipleUnits(html);

  for (const unit of units) {
    const analytics = await generateUnitAnalytics(unit, overview.address);
    if (analytics) {
      displayUnitAnalytics(unit, analytics);
    }
  }
}

const cheerio = require('cheerio');

function parseOverview(html) {
  const $ = cheerio.load(html);
  const address = $('.property-address').text().trim();
  return { address };
}

function parseMultipleUnits(html) {
  const $ = cheerio.load(html);
  const units = [];

  $('.multiple-units-table tbody tr').each((i, row) => {
    const columns = $(row).find('td');
    const unit = {
      type: $(columns[0]).text().trim(),
      size: $(columns[1]).text().trim(),
      bedrooms: $(columns[2]).text().trim(),
      price: $(columns[3]).text().trim(),
    };
    units.push(unit);
  });

  return units;
}

async function getInventoryValuations(inventory) {
  const valuations = [];
  const pslCode = await getPslCode();

  if (pslCode) {
    for (const item of inventory) {
      const sizeSqm = parseFloat(item.size) * 0.092903; // Convert sqft to sqm
      const bedrooms = parseInt(item.bedrooms);
      const segment = item.propertyType.toLowerCase().includes('apartment') ? 2 : 1; // 2 for apartment, 1 for villa

      const valuation = await getValuation(pslCode, segment, sizeSqm, bedrooms);
      valuations.push(valuation);
    }
  }

  return valuations;
}

function calculateAveragePriceForBedrooms(valuationData, bedrooms) {
  const sales = valuationData.estimate.sale.comparables.government_transactions;
  const rents = valuationData.estimate.rent.comparables.government_transactions;
  let saleTotal = 0;
  let saleCount = 0;
  let rentTotal = 0;
  let rentCount = 0;

  for (const transaction of sales) {
    if (parseInt(transaction.room_type) === bedrooms) {
      saleTotal += transaction.price_aed;
      saleCount++;
    }
  }

  for (const transaction of rents) {
    if (parseInt(transaction.room_type) === bedrooms) {
      rentTotal += transaction.price_aed;
      rentCount++;
    }
  }

  return {
    sale: saleCount > 0 ? saleTotal / saleCount : 0,
    rent: rentCount > 0 ? rentTotal / rentCount : 0,
  };
}

function getRecentTransactions(valuationData) {
  return {
    sales: valuationData.estimate.sale.comparables.government_transactions.slice(0, 3),
    rents: valuationData.estimate.rent.comparables.government_transactions.slice(0, 3),
  };
}

function calculateAveragePriceForType(valuationData) {
  const sales = valuationData.estimate.sale.comparables.government_transactions;
  let total = 0;

  for (const transaction of sales) {
    total += transaction.price_aed;
  }

  return sales.length > 0 ? total / sales.length : 0;
}

async function generateUnitAnalytics(unit, address) {
  const pslCode = await getPslCode(address);
  if (!pslCode) {
    return null;
  }

  const sizeSqm = parseFloat(unit.size) * 0.092903; // Convert sqft to sqm
  const bedrooms = parseInt(unit.bedrooms);
  const segment = unit.type.toLowerCase().includes('apartment') ? 2 : 1; // 2 for apartment, 1 for villa

  const valuationData = await getValuation(pslCode, segment, sizeSqm, bedrooms);
  if (!valuationData) {
    return null;
  }

  const averagePrices = calculateAveragePriceForBedrooms(valuationData, bedrooms);
  const recentTransactions = getRecentTransactions(valuationData);
  const averagePriceForType = calculateAveragePriceForType(valuationData);

  return {
    averagePrices,
    recentTransactions,
    averagePriceForType,
  };
}

function displayUnitAnalytics(unit, analytics) {
  const container = document.querySelector('.multiple-units-container');
  if (!container) {
    console.error('Multiple units container not found');
    return;
  }

  const unitContainer = document.createElement('div');
  unitContainer.classList.add('unit-analytics');
  unitContainer.innerHTML = `
    <h2>Analytics for ${unit.type}</h2>
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
  `;

  container.appendChild(unitContainer);
}

module.exports = {
  getPslCode,
  getValuation,
  calculateAveragePrices,
  displayAveragePrices,
  displayRecentTransactions,
  createPricePerSqFtGraph,
  parseInventoryTable,
  getInventoryValuations,
  displayAnalytics,
  main,
};
