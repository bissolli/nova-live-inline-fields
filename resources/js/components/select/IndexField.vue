<template>
    <div class="relative flex items-stretch" @dblclick="showSelect" :class="{ flashGreen }">
        <span v-show="!isSelectVisible" class="whitespace-no-wrap border-b border-dashed border-color cursor-pointer">{{ field.value }}</span>
        <select-control
            v-show="isSelectVisible"
            v-model.trim="value"
            class="form-control form-input-bordered"
            ref="select"
            @change="save"
            v-on-clickaway="cancel"
            :class="{ flashRed }"
            :options="field.options"
            >
            <option value="" disabled
                {{ __('Choose an option') }}
            </option>
        </select-control>
        <svg class="absolute pin-x pin-y m-auto rotating text-80 w-4 h-4" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" v-show="loading">
            <path fill="currentColor" d="M457.373 9.387l-50.095 50.102C365.411 27.211 312.953 8 256 8 123.228 8 14.824 112.338 8.31 243.493 7.971 250.311 13.475 256 20.301 256h10.015c6.352 0 11.647-4.949 11.977-11.293C48.159 131.913 141.389 42 256 42c47.554 0 91.487 15.512 127.02 41.75l-53.615 53.622c-20.1 20.1-5.855 54.628 22.627 54.628H480c17.673 0 32-14.327 32-32V32.015c0-28.475-34.564-42.691-54.627-22.628zM480 160H352L480 32v128zm11.699 96h-10.014c-6.353 0-11.647 4.949-11.977 11.293C463.84 380.203 370.504 470 256 470c-47.525 0-91.468-15.509-127.016-41.757l53.612-53.616c20.099-20.1 5.855-54.627-22.627-54.627H32c-17.673 0-32 14.327-32 32v127.978c0 28.614 34.615 42.641 54.627 22.627l50.092-50.096C146.587 484.788 199.046 504 256 504c132.773 0 241.176-104.338 247.69-235.493.339-6.818-5.165-12.507-11.991-12.507zM32 480V352h128L32 480z" class=""></path>
        </svg>
    </div>
</template>

<script>
    import { FormField, HandlesValidationErrors } from 'laravel-nova'
    import { mixin as clickaway } from 'vue-clickaway'
    import {each, tap} from "lodash";

    export default {
        mixins: [HandlesValidationErrors, FormField, clickaway],

        props: ['resourceName', 'field'],

        data() {
            return {
                isSelectVisible: false,
                loading: false,
                flashRed: false,
                flashGreen: false
            }
        },

        methods: {

            getDisplayValue() {
                return this.field.displayUsingLabels 
                    ? this.field.options.find(obj => obj.value === this.value).label
                    : this.value;
            },

            getValue() {
                return this.field.displayUsingLabels 
                    ? this.field.options.find(obj => obj.label === this.value).value
                    : this.value;
            },

            showSelect() {
                this.isSelectVisible = true;
                this.$nextTick(() => {
                    console.log(this.getValue());
                    this.$refs.select.value = this.getValue();
                })
            },

            save() {
                if (this.field.value == this.value) return;

                this.loading = true;

                Nova.request()
                    .post(
                        `/live-inline-fields/update/${this.resourceName}`,
                        {
                            id: this.field.id,
                            attribute: this.field.attribute,
                            value: this.value
                        }
                    )
                    .then(() => {
                        this.field.value = this.getDisplayValue();
                        this.isSelectVisible = false;
                        this.flashesInGreen();                        
                    })
                    .catch(error => {
                        this.flashesInRed();

                        let validationErrors = [];

                        if (error.response.status === 422) {
                            error.response.data.errors.value.forEach((errorMessage) => {
                                validationErrors.push(errorMessage.replace('value', this.field.name));
                            });
                        } else {
                            validationErrors.push('Ops, something went wrong.')
                        }

                        this.$toasted.error(validationErrors.join('<br>'));
                    })
                    .then(() => {
                        this.loading = false;
                    })
            },

            flashesInGreen() {
                this.flashGreen = true;
                setTimeout(() => (this.flashGreen = false), 1000);
            },
            
            flashesInRed() {
                this.flashRed = true;
                setTimeout(() => (this.flashRed = false), 1000);                
            },
            
            cancel() {
                this.isSelectVisible = false;
                this.value = this.field.value
            }
        }
    }
</script>

<style lang="scss" scoped>
    select {
        width: 100%;
        max-width: 100%;
    }

    @-webkit-keyframes rotating {
        from{
            transform: rotate(0deg);
        }
        to{
            transform: rotate(360deg);
        }
    }

    .rotating {
        animation: rotating 2s linear infinite;
    }

    @-webkit-keyframes flash-green {
        50% {
            border-color: green;
            color: green;
        }
    }

    .flashGreen {
        animation-name: flash-green;
        animation-duration: 500ms;
        animation-iteration-count: 1;
        animation-timing-function: ease-in-out;
    }

    @-webkit-keyframes flash-red {
        50% {
            border-color: #f00;
            color: #f00;
        }
    }

    .flashRed {
        animation-name: flash-red;
        animation-duration: 500ms;
        animation-iteration-count: 1;
        animation-timing-function: ease-in-out;
    }
</style>
