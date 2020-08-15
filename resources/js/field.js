Nova.booting((Vue, router, store) => {

    // Input Live Field
    Vue.component('index-input-live-field', require('./components/input/IndexField'))
    Vue.component('detail-input-live-field', require('./components/input/DetailField'))
    Vue.component('form-input-live-field', require('./components/input/FormField'))
  
    // Select Live Field
    Vue.component('index-select-live-field', require('./components/select/IndexField'))
    Vue.component('detail-select-live-field', require('./components/select/DetailField'))
    Vue.component('form-select-live-field', require('./components/select/FormField'))
  
    // Time Live Field
    Vue.component('index-time-live-field', require('./components/time/IndexField'))
    Vue.component('detail-time-live-field', require('./components/time/DetailField'))
    Vue.component('form-time-live-field', require('./components/time/FormField'))
  
  });
  