<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <select-control
                :id="field.name"
                v-model.trim="value"
                class="w-full form-control form-select"
                :class="errorClasses"
                :options="field.options"
                :selected="value"
                :disabled="isReadonly">

                <option value="" disabled>
                    {{ __('Choose an option') }}
                </option>
            </select-control>
        </template>
    </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    methods: {
        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.value = this.field.value || ''
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.field.attribute, this.value || '')
        },

        /**
         * Update the field's internal value.
         */
        handleChange(value) {
            this.value = value
        },
    },
}
</script>
