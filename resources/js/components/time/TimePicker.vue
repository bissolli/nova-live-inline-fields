<template>
    <input
        :disabled="disabled"
        :dusk="field.attribute"
        :class="{'!cursor-not-allowed': disabled}"
        :value="value"
        :name="field.name"
        ref="timePicker"
        type="text"
        :placeholder="placeholder"
        @blur="updatePickerValue"
        @keyup.enter="updatePickerValue"
        @keyup.esc="cancel"
    />  
</template>

<script>
import flatpickr from 'flatpickr'
import 'flatpickr/dist/themes/airbnb.css'

export default {
    props: {
        field: {
            required: true,
        },
        value: {
            required: false,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        placeholder: {
            type: String
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        twelveHourTime: {
            type: Boolean,
            default: true,
        },
        minuteIncrement: {
            type: Number,
            default: 5,
        }
    },

    data: () => ({ flatpickr: null }),

    computed: {
        dateFormat() {
            return this.twelveHourTime ? 'h:i K' : 'H:i';
        }
    },

    mounted() {
        this.$nextTick(() => {
            this.flatpickr = flatpickr(this.$refs.timePicker, {
                enableTime: true,
                noCalendar: true,
                dateFormat: this.dateFormat,
                allowInput: true,
                time_24hr: !this.twelveHourTime,
                minuteIncrement: this.minuteIncrement,
                onClose: this.onChange,
                onValueUpdate: this.onChange
            })
        })
    },

    methods: {
        cancel() {
            const newDateTime = this.flatpickr.parseDate(this.value, this.dateFormat);
            this.flatpickr.setDate(newDateTime)
        },
        updatePickerValue(event) {
            const newValue = event.target.value
            const newDateTime = this.flatpickr.parseDate(newValue, this.dateFormat);

            this.flatpickr.setDate(newDateTime)
        },
        enter(event) {
            this.updatePickerValue(event);
        },
        onChange(_, timeString) {
            this.$emit('change', timeString)
        }
    },
}
</script>

<style scoped>
.\!cursor-not-allowed {
    cursor: not-allowed !important;
}
</style>
