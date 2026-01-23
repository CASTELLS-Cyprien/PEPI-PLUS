import './styles/app.css';

import flatpickr from "flatpickr";
import { French } from "flatpickr/dist/l10n/fr.js";
import "flatpickr/dist/flatpickr.css";

document.addEventListener('DOMContentLoaded', function () {
    const initDateRange = (containerSelector) => {
        const containers = document.querySelectorAll(containerSelector);

        containers.forEach(container => {
            const rangeInput = container.querySelector('.js-datepicker-range');
            const startInput = container.querySelector('.js-datepicker-start');
            const endInput = container.querySelector('.js-datepicker-end');

            if (rangeInput && startInput && endInput) {
                flatpickr(rangeInput, {
                    mode: "range",
                    dateFormat: "d/m/Y",
                    locale: French,
                    onChange: function (selectedDates) {
                        if (selectedDates.length === 2) {
                            const formatDate = (date) => {
                                const year = date.getFullYear();
                                const month = String(date.getMonth() + 1).padStart(2, '0');
                                const day = String(date.getDate()).padStart(2, '0');
                                return `${year}-${month}-${day}`;
                            };
                            startInput.value = formatDate(selectedDates[0]);
                            endInput.value = formatDate(selectedDates[1]);
                        } else if (selectedDates.length === 0) {
                            startInput.value = '';
                            endInput.value = '';
                        }
                    }
                });
            }
        });
    };
    
    initDateRange('.js-date-range-container');
});