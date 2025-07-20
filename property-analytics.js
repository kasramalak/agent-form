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
  const inventory = parseInventoryTable(html);
  const valuations = await getInventoryValuations(inventory);
  displayAnalytics(valuations);
}

const cheerio = require('cheerio');

function parseInventoryTable(html) {
  const $ = cheerio.load(html);
  const inventory = [];

  $('.inventory-table tbody tr').each((i, row) => {
    const columns = $(row).find('td');
    const property = {
      title: $(columns[0]).text().trim(),
      propertyType: $(columns[1]).text().trim(),
      price: $(columns[2]).text().trim(),
      size: $(columns[3]).text().trim(),
      bedrooms: $(columns[4]).text().trim(),
    };
    inventory.push(property);
  });

  return inventory;
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

function displayAnalytics(valuations) {
  const container = document.getElementById('analytics-container');
  if (!container) {
    console.error('Analytics container not found');
    return;
  }

  for (const valuation of valuations) {
    const valuationContainer = document.createElement('div');
    valuationContainer.classList.add('valuation-item');

    const averagePrices = calculateAveragePrices(valuation);
    const averagePricesElement = document.createElement('div');
    averagePricesElement.innerHTML = `
      <h3>Average Prices</h3>
      <p><strong>Sale (1-bed):</strong> AED ${averagePrices.sale['1-bed'].average.toFixed(2)}</p>
      <p><strong>Sale (2-bed):</strong> AED ${averagePrices.sale['2-bed'].average.toFixed(2)}</p>
      <p><strong>Sale (3-bed):</strong> AED ${averagePrices.sale['3-bed'].average.toFixed(2)}</p>
      <p><strong>Rent (1-bed):</strong> AED ${averagePrices.rent['1-bed'].average.toFixed(2)}</p>
      <p><strong>Rent (2-bed):</strong> AED ${averagePrices.rent['2-bed'].average.toFixed(2)}</p>
      <p><strong>Rent (3-bed):</strong> AED ${averagePrices.rent['3-bed'].average.toFixed(2)}</p>
    `;
    valuationContainer.appendChild(averagePricesElement);

    const recentTransactionsElement = document.createElement('div');
    recentTransactionsElement.innerHTML = '<h3>Recent Transactions</h3>';
    displayRecentTransactions(valuation); // This function needs to be adapted to append to the element
    valuationContainer.appendChild(recentTransactionsElement);

    const graphElement = document.createElement('div');
    graphElement.innerHTML = '<h3>Price per Sq Ft Graph</h3>';
    const canvas = document.createElement('canvas');
    graphElement.appendChild(canvas);
    createPricePerSqFtGraph(valuation, canvas); // This function needs to be adapted to draw on the canvas
    valuationContainer.appendChild(graphElement);

    container.appendChild(valuationContainer);
  }
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
