'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const labelColor = config.colors.textMuted,
    headingColor = config.colors.headingColor,
    borderColor = config.colors.borderColor,
    legendColor = config.colors.bodyColor,
    fontFamily = config.fontFamily;

  const darkColors = ['#4B5563', '#2563EB', '#D97706', '#DC2626', '#6B7280'];

  let requestChart = null;

  const requestContainer = document.querySelector('#requestStatusPieChart')?.parentElement;

  // Spinner creation with ID support
  const createSpinner = (id = '') => {
    const spinner = document.createElement('div');
    spinner.id = id;
    spinner.className = 'chart-spinner';
    spinner.innerHTML = `
      <div class="sk-fold">
        <div class="sk-fold-cube"></div>
        <div class="sk-fold-cube"></div>
        <div class="sk-fold-cube"></div>
        <div class="sk-fold-cube"></div>
      </div>
    `;
    return spinner;
  };

  function showSpinner(container, target = 'request') {
    if (!container) return;

    const id = target === 'request' ? 'requestChartSpinner' : 'vaultChartSpinner';
    hideSpinner(container); // pastikan tidak duplikat
    container.appendChild(createSpinner(id));
  }

  function hideSpinner(container) {
    if (!container) return;
    const spinner = container.querySelector('.chart-spinner');
    if (spinner) spinner.remove();
  }

  function renderVaultActivityChart(data) {
    const el = document.querySelector('#vaultActivityChart');
    if (!el) return;

    const vaultChart = new ApexCharts(el, {
      chart: {
        height: 320,
        type: 'bar',
        parentHeightOffset: 0,
        toolbar: { show: false }
      },
      series: [
        { name: 'Created', data: data?.created || [] },
        { name: 'Accessed', data: data?.accessed || [] },
        { name: 'Rotated', data: data?.rotated || [] }
      ],
      colors: [darkColors[1], darkColors[2], darkColors[3]],
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '30%',
          borderRadius: 4,
          endingShape: 'rounded'
        }
      },
      dataLabels: { enabled: false },
      stroke: {
        show: true,
        width: 4,
        colors: ['transparent']
      },
      xaxis: {
        categories: data?.labels || [],
        labels: {
          style: {
            fontSize: '13px',
            fontFamily,
            colors: labelColor
          }
        }
      },
      yaxis: {
        title: { text: 'Jumlah Event' },
        labels: {
          style: {
            fontSize: '13px',
            fontFamily,
            colors: labelColor
          }
        }
      },
      fill: { opacity: 1 },
      legend: {
        position: 'bottom',
        fontSize: '14px',
        labels: { colors: legendColor }
      },
      tooltip: {
        y: { formatter: val => `${val} event` }
      },
      grid: {
        borderColor,
        strokeDashArray: 4
      },
      responsive: [
        {
          breakpoint: 768,
          options: {
            chart: { height: 280 },
            plotOptions: { bar: { columnWidth: '50%' } }
          }
        }
      ]
    });

    vaultChart.render();
  }

  function renderRequestStatusChart(data) {
    const el = document.querySelector('#requestStatusPieChart');
    if (!el) return;
    if (requestChart) requestChart.destroy();

    requestChart = new ApexCharts(el, {
      chart: {
        type: 'pie',
        height: 320,
        animations: { enabled: true, easing: 'easeinout', speed: 500 }
      },
      series: data?.series || [],
      labels: data?.labels || [],
      colors: [darkColors[2], darkColors[1], darkColors[3], darkColors[4]],
      legend: {
        position: 'bottom',
        fontSize: '14px',
        labels: { colors: legendColor }
      },
      dataLabels: {
        enabled: true,
        style: {
          fontSize: '13px',
          fontFamily,
          colors: ['#fff']
        }
      },
      tooltip: {
        y: { formatter: val => `${val} request` }
      },
      states: {
        active: { filter: { type: 'none' } },
        hover: { filter: { type: 'lighten', value: 0.05 } }
      },
      stroke: { show: false },
      plotOptions: {
        pie: {
          expandOnClick: true,
          donut: { size: '0%' }
        }
      }
    });

    requestChart.render();
  }

  function fetchPieChartData(month, year) {
    showSpinner(requestContainer, 'request');

    fetch(`/dashboard/chart-data?month=${month}&year=${year}`)
      .then(res => res.json())
      .then(data => {
        renderRequestStatusChart(data.requestStatusData);
      })
      .catch(err => console.error('Gagal fetch chart data', err))
      .finally(() => {
        hideSpinner(requestContainer);
      });
  }

  // Default to current month/year
  const now = new Date();
  const currentMonth = String(now.getMonth() + 1).padStart(2, '0');
  const currentYear = String(now.getFullYear());

  const monthSelects = document.querySelectorAll('.chart-filter-month');
  const yearSelects = document.querySelectorAll('.chart-filter-year');

  monthSelects.forEach(select => {
    select.value = currentMonth;
  });
  yearSelects.forEach(select => {
    select.value = currentYear;
  });

  function attachFilterListeners() {
    monthSelects.forEach(select => {
      select.addEventListener('change', () => {
        const selectedMonth = select.value;
        const selectedYear = document.querySelector(`#${select.dataset.year}`)?.value || currentYear;
        fetchPieChartData(selectedMonth, selectedYear);
      });
    });

    yearSelects.forEach(select => {
      select.addEventListener('change', () => {
        const selectedYear = select.value;
        const monthSelect = document.querySelector(`#${select.dataset.month}`);
        const selectedMonth = monthSelect?.value || currentMonth;
        fetchPieChartData(selectedMonth, selectedYear);
      });
    });
  }

  attachFilterListeners();

  // Initial render
  renderVaultActivityChart(window.auditStats);
  renderRequestStatusChart(window.requestStatusData);
});
