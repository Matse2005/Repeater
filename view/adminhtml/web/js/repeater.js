(function () {
    'use strict';

    class RepeaterWidget {
        constructor(config) {
            this.containerId = config.containerId;
            this.elementId = config.elementId;
            this.hiddenId = config.hiddenId;
            this.label = config.label;
            this.fields = config.fields;
            this.existingRows = config.existingRows;
        }

        get container() {
            return document.getElementById(this.containerId);
        }

        init() {
            this.renderRows(this.existingRows);
            window['addRow_' + this.elementId] = () => this.addRow();
            window['saveRows_' + this.elementId] = () => this.saveRows();
        }

        saveRows() {
            let valid = true;

            this.container.querySelectorAll('[data-field][required]').forEach((inp) => {
                if (!inp.value.trim()) {
                    inp.classList.add('mage-error');
                    valid = false;
                } else {
                    inp.classList.remove('mage-error');
                }
            });

            if (!valid) return;

            const rows = [];
            this.container.querySelectorAll('.repeater-group').forEach((group) => {
                const obj = {};
                group.querySelectorAll('input[data-field], textarea[data-field], select[data-field]').forEach((inp) => {
                    obj[inp.dataset.field] = inp.value;
                });
                rows.push(obj);
            });

            const json = JSON.stringify(rows);

            const hidden = document.getElementById(this.hiddenId);
            if (hidden) hidden.value = json;

            const original = document.getElementById(this.elementId);
            if (original) original.value = json;
        }

        updateTitles() {
            this.container.querySelectorAll('.repeater-group__title').forEach((el, i) => {
                el.textContent = `${this.label} ${i + 1}`;
            });
        }

        appendGroup(val = {}, idx) {
            const group = document.createElement('div');
            group.className = 'repeater-group';

            const header = document.createElement('div');
            header.className = 'repeater-group__header';

            const title = document.createElement('strong');
            title.className = 'repeater-group__title';
            title.textContent = `${this.label} ${idx + 1}`;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'action-default repeater-group__remove';
            removeBtn.textContent = '✕ Remove';
            removeBtn.addEventListener('click', () => {
                group.remove();
                this.updateTitles();
                this.saveRows();
            });

            header.append(title, removeBtn);
            group.appendChild(header);

            this.fields.forEach((field) => {
                const row = document.createElement('div');
                row.classList.add('admin__field', 'field', 'field-title');

                if (field.data.required) {
                    row.classList.add('required', '_required');
                }

                if (field.label) {
                    const labelWrapper = document.createElement('div');
                    labelWrapper.innerHTML = field.label.replace(/\{\{idx\}\}/g, idx);
                    row.appendChild(labelWrapper.firstChild);
                }

                const control = document.createElement('div');
                control.classList.add('admin__field-control', 'control');
                control.innerHTML = field.html
                    .replace(/\{\{idx\}\}/g, idx)
                    .replace(/\{\{value\}\}/g, escapeHtml(val[field.data.name] || ''));

                const input = control.querySelector('[data-field]');
                if (input) input.addEventListener('input', () => this.saveRows());

                row.appendChild(control);
                group.appendChild(row);
            });

            this.container.appendChild(group);
        }

        renderRows(rows) {
            this.container.innerHTML = '';
            rows.forEach((val, idx) => this.appendGroup(val, idx));
        }

        addRow() {
            const idx = this.container.querySelectorAll('.repeater-group').length;
            this.appendGroup({}, idx);
            this.saveRows();
        }
    }

    function escapeHtml(str) {
        return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    window.RepeaterWidget = RepeaterWidget;
})();
