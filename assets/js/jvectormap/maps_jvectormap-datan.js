let disabled = "#F3F5F6"; // Background color of datan

let colorsRegions2015 = {
  "FR-X1": "#F78080",
  "FR-X2": "#F78080",
  "FR-X3": "#54FEFF",
  "FR-X4": "#1A66CC",
  "FR-X5": "#F78080",
  "FR-X6": "#ADC1FD",
  "FR-X7": "#1A66CC",
  "FR-E": "#F78080",
  "FR-F": "#F78080",
  "FR-H": "#FBCC33",
  "FR-J": "#ADC1FD",
  "FR-R": "#1A66CC",
  "FR-U": "#1A66CC",
  "FR-GF": "#fac0c0",
  "FR-GP": "#fac0c0",
  "FR-MQ": "#FBCC33",
  "FR-RE": "#1A66CC",
  "FR-YT": disabled
}

let colorsRegions2021 = {
  // https://jvectormap.com/maps/countries/france-regions-2016/
  "FR-X1": "#F78080",
  "FR-X2": "#F78080",
  "FR-X3": "#54FEFF",
  "FR-X4": "#54FEFF",
  "FR-X5": "#F78080",
  "FR-X6": "#1A66CC",
  "FR-X7": "#1A66CC",
  "FR-E": "#F78080",
  "FR-F": "#F78080",
  "FR-H": "#FBCC33",
  "FR-J": "#1A66CC",
  "FR-R": "#54FEFF",
  "FR-U": "#1A66CC",
  "FR-GF": "#F78080",
  "FR-GP": "#54FEFF",
  "FR-MQ": "#F78080",
  "FR-RE": "#F78080",
  "FR-YT": disabled
}

let Regions2015Disabled = ['FR-YT'];

// Converting CSV to JSON ==> https://www.convertcsv.com/csv-to-json.htm
let colorsDpt2015 = {
   "FR-01":"#0066cc",
   "FR-02":"#00FFFF",
   "FR-03":"#0066cc",
   "FR-04":"#FF8080",
   "FR-05":"#0066cc",
   "FR-06":"#0066cc",
   "FR-07":"#FF8080",
   "FR-08":"#0066cc",
   "FR-09":"#FF8080",
   "FR-10":"#adc1fd",
   "FR-11":"#FF8080",
   "FR-12":"#00FFFF",
   "FR-13":"#0066cc",
   "FR-14":"#00FFFF",
   "FR-15":"#0066cc",
   "FR-16":"#adc1fd",
   "FR-17":"#0066cc",
   "FR-18":"#0066cc",
   "FR-19":"#0066cc",
   "FR-21":"#00FFFF",
   "FR-22":"#0066cc",
   "FR-23":"#0066cc",
   "FR-24":"#FF8080",
   "FR-25":"#adc1fd",
   "FR-26":"#0066cc",
   "FR-27":"#0066cc",
   "FR-28":"#0066cc",
   "FR-29":"#FF8080",
   "FR-2A":"#05903f",
   "FR-2B":"#ffd1dc",
   "FR-30":"#FF8080",
   "FR-31":"#FF8080",
   "FR-32":"#FF8080",
   "FR-33":"#FF8080",
   "FR-34":"#FF8080",
   "FR-35":"#FF8080",
   "FR-36":"#0066cc",
   "FR-37":"#00FFFF",
   "FR-38":"#0066cc",
   "FR-39":"#adc1fd",
   "FR-40":"#FF8080",
   "FR-41":"#00FFFF",
   "FR-42":"#0066cc",
   "FR-43":"#00FFFF",
   "FR-44":"#FF8080",
   "FR-45":"#0066cc",
   "FR-46":"#FF8080",
   "FR-47":"#FF8080",
   "FR-48":"#FF8080",
   "FR-49":"#00FFFF",
   "FR-50":"#0066cc",
   "FR-51":"#0066cc",
   "FR-52":"#0066cc",
   "FR-53":"#00FFFF",
   "FR-54":"#FF8080",
   "FR-55":"#0066cc",
   "FR-56":"#0066cc",
   "FR-57":"#00FFFF",
   "FR-58":"#FF8080",
   "FR-59":"#0066cc",
   "FR-60":"#0066cc",
   "FR-61":"#00FFFF",
   "FR-62":"#FF8080",
   "FR-63":"#ffd1dc",
   "FR-64":"#ff9900",
   "FR-65":"#ffd1dc",
   "FR-66":"#FF8080",
   "FR-67":"#0066cc",
   "FR-68":"#0066cc",
   "FR-69":"#0066cc",
   "FR-70":"#FF8080",
   "FR-71":"#0066cc",
   "FR-72":"#0066cc",
   "FR-73":"#0066cc",
   "FR-74":"#adc1fd",
   "FR-76":"#00FFFF",
   "FR-77":"#0066cc",
   "FR-78":"#0066cc",
   "FR-79":"#0066cc",
   "FR-80":"#0066cc",
   "FR-81":"#FF8080",
   "FR-82":"#dddddd",
   "FR-83":"#0066cc",
   "FR-84":"#0066cc",
   "FR-85":"#adc1fd",
   "FR-86":"#0066cc",
   "FR-87":"#FF8080",
   "FR-88":"#0066cc",
   "FR-89":"#00FFFF",
   "FR-90":"#0066cc",
   "FR-91":"#0066cc",
   "FR-92":"#0066cc",
   "FR-93":"#FF8080",
   "FR-94":"#dd0000",
   "FR-95":"#adc1fd",
   "FR-GP":"#FF8080",
   "FR-RE":"#00FFFF",
   "FR-YT":"#0066cc",
   "FR-GF":disabled,
   "FR-MQ":disabled,
   "FR-75":disabled
}

let Dpts2015Disabled = ['FR-GF', 'FR-MQ', 'FR-75'];

$(function() {
  $('#map-regions').vectorMap({
    map: 'fr_regions_2016_merc',
    hoverOpacity: 0.7,
    hoverColor: false,
    backgroundColor:'#f3f5f6',
    series: {
      regions: [{
        values: colorsRegions2021
      }]
    },
    onRegionTipShow: function ( e, label, code ) {
       if ( Regions2015Disabled.indexOf(code) >= 0 ) {
         e.preventDefault();
         document.body.style.cursor = 'default';
       }
    },
    onRegionOver: function ( e, code) {
      console.log(code);
      if ( Regions2015Disabled.indexOf(code) >= 0 ) {
        e.preventDefault();
      }
    }
  });
});

$(function() {
  $('#map-departements').vectorMap({
    map: 'fr_merc',
    hoverOpacity: 0.7,
    hoverColor: false,
    backgroundColor:'#f3f5f6',
    series: {
      regions: [{
        values: colorsDpt2015
      }]
    },
    onRegionTipShow: function ( e, label, code ) {
       if ( Dpts2015Disabled.indexOf(code) >= 0 ) {
         e.preventDefault();
         document.body.style.cursor = 'default';
       }
    },
    onRegionOver: function ( e, code) {
      console.log(code);
      if ( Dpts2015Disabled.indexOf(code) >= 0 ) {
        e.preventDefault();
      }
    }
  });
});
