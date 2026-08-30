/**
 * DGTCP — Dashboard ApexCharts
 * Initialise les graphiques à partir de window.dgtcpDashboard
 */
'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const data = window.dgtcpDashboard;
  if (!data || typeof ApexCharts === 'undefined') return;

  const colors = {
    primary: '#009739',
    warning: '#FFD600',
    danger: '#E30613',
    info: '#00cfe8',
    secondary: '#a8aaae',
    success: '#28c76f',
  };

  const chartFont = 'Public Sans, sans-serif';
  const formatFcfa = (val) => {
    if (val >= 1000) return (val / 1000).toFixed(1) + ' Md';
    return val.toFixed(1) + ' M';
  };

  // Sparklines dans les cartes KPI
  const sparklineConfig = (series, color) => ({
    chart: { type: 'area', height: 55, sparkline: { enabled: true }, toolbar: { show: false } },
    stroke: { curve: 'smooth', width: 2 },
    fill: {
      type: 'gradient',
      gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [0, 100] },
    },
    colors: [color],
    series: [{ data: series }],
    tooltip: { enabled: false },
  });

  ['sparkDemandes', 'sparkRecettes', 'sparkValidations', 'sparkEnCours'].forEach((id, i) => {
    const el = document.querySelector('#' + id);
    const seriesMap = [data.sparklines?.demandes, data.sparklines?.recettes, data.sparklines?.validations, data.sparklines?.demandes];
    const colorMap = [colors.primary, colors.warning, colors.success, colors.danger];
    if (el && seriesMap[i]?.length) {
      new ApexCharts(el, sparklineConfig(seriesMap[i], colorMap[i])).render();
    }
  });

  // Évolution mensuelle — area chart
  const evolutionEl = document.querySelector('#chartEvolution');
  if (evolutionEl && data.monthly) {
    new ApexCharts(evolutionEl, {
      chart: {
        height: 340,
        type: 'area',
        toolbar: { show: true },
        fontFamily: chartFont,
        zoom: { enabled: true },
      },
      series: [
        { name: 'Demandes (M FCFA)', data: data.monthly.montants },
        { name: 'Recettes (M FCFA)', data: data.monthly.recettes },
        { name: 'Nb demandes', data: data.monthly.counts },
      ],
      colors: [colors.primary, colors.warning, colors.info],
      stroke: { curve: 'smooth', width: 2 },
      fill: {
        type: 'gradient',
        gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] },
      },
      xaxis: { categories: data.monthly.labels, labels: { style: { fontSize: '12px' } } },
      yaxis: {
        labels: {
          formatter: (v) => (v < 50 ? Math.round(v) : formatFcfa(v)),
        },
      },
      legend: { position: 'top', horizontalAlign: 'left' },
      grid: { borderColor: '#e7e7e8', strokeDashArray: 4 },
      tooltip: { shared: true, intersect: false },
    }).render();
  }

  // Répartition par statut — donut
  const statusEl = document.querySelector('#chartStatus');
  if (statusEl && data.status) {
    new ApexCharts(statusEl, {
      chart: { type: 'donut', height: 320, fontFamily: chartFont },
      series: data.status.series,
      labels: data.status.labels,
      colors: data.status.colors,
      legend: { position: 'bottom' },
      plotOptions: {
        pie: {
          donut: {
            size: '72%',
            labels: {
              show: true,
              name: { fontSize: '14px' },
              value: { fontSize: '22px', fontWeight: 600, formatter: (v) => v },
              total: {
                show: true,
                label: 'Total',
                formatter: (w) => w.globals.seriesTotals.reduce((a, b) => a + b, 0),
              },
            },
          },
        },
      },
      dataLabels: { enabled: false },
    }).render();
  }

  // Top postes — bar chart groupé
  const postesEl = document.querySelector('#chartTopPostes');
  if (postesEl && data.topPostes?.labels?.length) {
    new ApexCharts(postesEl, {
      chart: { type: 'bar', height: 340, toolbar: { show: true }, fontFamily: chartFont },
      series: [
        { name: 'Demandes (M FCFA)', data: data.topPostes.demandes },
        { name: 'Recettes (M FCFA)', data: data.topPostes.recettes },
      ],
      colors: [colors.primary, colors.warning],
      plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 6 } },
      xaxis: { categories: data.topPostes.labels, labels: { rotate: -35, style: { fontSize: '11px' } } },
      yaxis: { labels: { formatter: (v) => formatFcfa(v) } },
      legend: { position: 'top' },
      grid: { borderColor: '#e7e7e8', strokeDashArray: 4 },
      dataLabels: { enabled: false },
    }).render();
  }

  // Catégories de personnel — radial bar
  const categoriesEl = document.querySelector('#chartCategories');
  if (categoriesEl && data.categories) {
    new ApexCharts(categoriesEl, {
      chart: { type: 'radar', height: 340, toolbar: { show: false }, fontFamily: chartFont },
      series: [{ name: 'Montants (M FCFA)', data: data.categories.series }],
      xaxis: { categories: data.categories.labels },
      yaxis: { show: false },
      colors: [colors.primary],
      markers: { size: 4 },
      stroke: { width: 2 },
      fill: { opacity: 0.15 },
      tooltip: { y: { formatter: (v) => v + ' M FCFA' } },
    }).render();
  }

  // Taux de validation — radial gauge
  const gaugeEl = document.querySelector('#chartGauge');
  if (gaugeEl && data.kpis) {
    new ApexCharts(gaugeEl, {
      chart: { type: 'radialBar', height: 280, fontFamily: chartFont },
      series: [data.kpis.taux_validation],
      colors: [colors.primary],
      plotOptions: {
        radialBar: {
          hollow: { size: '62%' },
          track: { background: '#e7e7e8' },
          dataLabels: {
            name: { fontSize: '14px', color: '#6e6b7b', offsetY: 24 },
            value: { fontSize: '28px', fontWeight: 700, formatter: (v) => v + '%' },
          },
        },
      },
      labels: ['Taux de validation'],
    }).render();
  }

  // Barres empilées — validations mensuelles
  const validationsEl = document.querySelector('#chartValidations');
  if (validationsEl && data.monthly) {
    new ApexCharts(validationsEl, {
      chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: chartFont },
      series: [{ name: 'Validations', data: data.monthly.approuves }],
      colors: [colors.success],
      plotOptions: { bar: { borderRadius: 6, columnWidth: '45%' } },
      xaxis: { categories: data.monthly.labels },
      grid: { borderColor: '#e7e7e8', strokeDashArray: 4 },
      dataLabels: { enabled: true, style: { fontSize: '11px' } },
    }).render();
  }

  // Comparaison recettes vs demandes — mixed bar/line
  const compareEl = document.querySelector('#chartCompare');
  if (compareEl && data.monthly) {
    new ApexCharts(compareEl, {
      chart: { type: 'line', height: 280, toolbar: { show: false }, fontFamily: chartFont },
      series: [
        { name: 'Demandes', type: 'column', data: data.monthly.montants },
        { name: 'Recettes', type: 'column', data: data.monthly.recettes },
        { name: 'Écart', type: 'line', data: data.monthly.montants.map((m, i) => +(m - (data.monthly.recettes[i] || 0)).toFixed(2)) },
      ],
      colors: [colors.primary, colors.warning, colors.danger],
      stroke: { width: [0, 0, 3], curve: 'smooth' },
      plotOptions: { bar: { columnWidth: '40%', borderRadius: 4 } },
      xaxis: { categories: data.monthly.labels },
      yaxis: { labels: { formatter: (v) => formatFcfa(v) } },
      legend: { position: 'top' },
      grid: { borderColor: '#e7e7e8', strokeDashArray: 4 },
    }).render();
  }
});
