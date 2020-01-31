import style from '../../sass/components/frontweb-toolbar.scss';

window.CMSToolbar = class CMSToolbar {
    constructor (data) {
        this.data = data;
        this.svgIcons = {
            'plus-circle': `<svg width="12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M384 240v32c0 6.6-5.4 12-12 12h-88v88c0 6.6-5.4 12-12 12h-32c-6.6 0-12-5.4-12-12v-88h-88c-6.6 0-12-5.4-12-12v-32c0-6.6 5.4-12 12-12h88v-88c0-6.6 5.4-12 12-12h32c6.6 0 12 5.4 12 12v88h88c6.6 0 12 5.4 12 12zm120 16c0 137-111 248-248 248S8 393 8 256 119 8 256 8s248 111 248 248zm-48 0c0-110.5-89.5-200-200-200S56 145.5 56 256s89.5 200 200 200 200-89.5 200-200z"></path></svg>`,
            'edit': `<svg width="12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M402.3 344.9l32-32c5-5 13.7-1.5 13.7 5.7V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h273.5c7.1 0 10.7 8.6 5.7 13.7l-32 32c-1.5 1.5-3.5 2.3-5.7 2.3H48v352h352V350.5c0-2.1.8-4.1 2.3-5.6zm156.6-201.8L296.3 405.7l-90.4 10c-26.2 2.9-48.5-19.2-45.6-45.6l10-90.4L432.9 17.1c22.9-22.9 59.9-22.9 82.7 0l43.2 43.2c22.9 22.9 22.9 60 .1 82.8zM460.1 174L402 115.9 216.2 301.8l-7.3 65.3 65.3-7.3L460.1 174zm64.8-79.7l-43.2-43.2c-4.1-4.1-10.8-4.1-14.8 0L436 82l58.1 58.1 30.9-30.9c4-4.2 4-10.8-.1-14.9z"></path></svg>`
        };

        this.render();
    }

    render () {
        if (!document.body) {
            return;
        }

        let head = document.getElementsByTagName("head")[0];
        let style = document.createElement('style');
        style.innerText = style;
        head.appendChild(style);

        document.body.insertAdjacentHTML("afterBegin", this.getHtml());

        try {
            this.indentFixedElements();
        } catch (e) {
            console.error(e);
        }

        this.initializeEvents();
    }

    getHtml () {
        return `
            <div class="_cms_toolbar">
                <ul class="_cms_toolbar-nav">
                    <li>
                        <a href="${this.data.urls.admin}">  
                            <svg width="10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M229.9 473.899l19.799-19.799c4.686-4.686 4.686-12.284 0-16.971L94.569 282H436c6.627 0 12-5.373 12-12v-28c0-6.627-5.373-12-12-12H94.569l155.13-155.13c4.686-4.686 4.686-12.284 0-16.971L229.9 38.101c-4.686-4.686-12.284-4.686-16.971 0L3.515 247.515c-4.686 4.686-4.686 12.284 0 16.971L212.929 473.9c4.686 4.686 12.284 4.686 16.971-.001z"></path></svg> ${this.data.localization.go_to_admin}
                        </a>
                    </li>
                    ${this.getControlsHtml()}
                </ul>
                <ul class="_cms_toolbar-nav right">
                    ${this.getSwitchHtml()}
                    ${this.getMaintenanceModeLabelHtml()}
                    ${this.getStatusHtml()}
                    <li>
                        <a id="_cms-user-info">
                            <img src="${this.data.user.avatar}" alt="User Avatar">
                            <span>${this.data.user.username}</span>

                            <svg width="8" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M151.5 347.8L3.5 201c-4.7-4.7-4.7-12.3 0-17l19.8-19.8c4.7-4.7 12.3-4.7 17 0L160 282.7l119.7-118.5c4.7-4.7 12.3-4.7 17 0l19.8 19.8c4.7 4.7 4.7 12.3 0 17l-148 146.8c-4.7 4.7-12.3 4.7-17 0z"></path></svg>
                        </a> 
                        <ul class="_cms-dropdown-menu">
                            <li class="_cms-dropdown-menu-user-header">
                                <p class="_cms-dropdown-menu-user-header-name">${this.data.user.name}</p>
                                <p class="_cms-dropdown-menu-user-header-info">
                                    ${this.data.localization.registered_since} ${this.data.user.registration}
                                </p>
                            </li>
                            <li class="_cms-dropdown-menu-divider"></li>
                            <li>
                                <a href="${this.data.urls.editAccount}">
                                    <svg width="12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M452.515 237l31.843-18.382c9.426-5.441 13.996-16.542 11.177-27.054-11.404-42.531-33.842-80.547-64.058-110.797-7.68-7.688-19.575-9.246-28.985-3.811l-31.785 18.358a196.276 196.276 0 0 0-32.899-19.02V39.541a24.016 24.016 0 0 0-17.842-23.206c-41.761-11.107-86.117-11.121-127.93-.001-10.519 2.798-17.844 12.321-17.844 23.206v36.753a196.276 196.276 0 0 0-32.899 19.02l-31.785-18.358c-9.41-5.435-21.305-3.877-28.985 3.811-30.216 30.25-52.654 68.265-64.058 110.797-2.819 10.512 1.751 21.613 11.177 27.054L59.485 237a197.715 197.715 0 0 0 0 37.999l-31.843 18.382c-9.426 5.441-13.996 16.542-11.177 27.054 11.404 42.531 33.842 80.547 64.058 110.797 7.68 7.688 19.575 9.246 28.985 3.811l31.785-18.358a196.202 196.202 0 0 0 32.899 19.019v36.753a24.016 24.016 0 0 0 17.842 23.206c41.761 11.107 86.117 11.122 127.93.001 10.519-2.798 17.844-12.321 17.844-23.206v-36.753a196.34 196.34 0 0 0 32.899-19.019l31.785 18.358c9.41 5.435 21.305 3.877 28.985-3.811 30.216-30.25 52.654-68.266 64.058-110.797 2.819-10.512-1.751-21.613-11.177-27.054L452.515 275c1.22-12.65 1.22-25.35 0-38zm-52.679 63.019l43.819 25.289a200.138 200.138 0 0 1-33.849 58.528l-43.829-25.309c-31.984 27.397-36.659 30.077-76.168 44.029v50.599a200.917 200.917 0 0 1-67.618 0v-50.599c-39.504-13.95-44.196-16.642-76.168-44.029l-43.829 25.309a200.15 200.15 0 0 1-33.849-58.528l43.819-25.289c-7.63-41.299-7.634-46.719 0-88.038l-43.819-25.289c7.85-21.229 19.31-41.049 33.849-58.529l43.829 25.309c31.984-27.397 36.66-30.078 76.168-44.029V58.845a200.917 200.917 0 0 1 67.618 0v50.599c39.504 13.95 44.196 16.642 76.168 44.029l43.829-25.309a200.143 200.143 0 0 1 33.849 58.529l-43.819 25.289c7.631 41.3 7.634 46.718 0 88.037zM256 160c-52.935 0-96 43.065-96 96s43.065 96 96 96 96-43.065 96-96-43.065-96-96-96zm0 144c-26.468 0-48-21.532-48-48 0-26.467 21.532-48 48-48s48 21.533 48 48c0 26.468-21.532 48-48 48z"></path></svg> ${this.data.localization.account_settings}
                                </a>
                            </li>
                            <li>
                                <a href="${this.data.urls.logout}" id="_cms-logout">                                   
                                    <svg width="12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M388.5 46.3C457.9 90.3 504 167.8 504 256c0 136.8-110.8 247.7-247.5 248C120 504.3 8.2 393 8 256.4 7.9 168 54 90.3 123.5 46.3c5.8-3.7 13.5-1.8 16.9 4.2l11.8 20.9c3.1 5.5 1.4 12.5-3.9 15.9C92.8 122.9 56 185.1 56 256c0 110.5 89.5 200 200 200s200-89.5 200-200c0-70.9-36.8-133.1-92.3-168.6-5.3-3.4-7-10.4-3.9-15.9l11.8-20.9c3.3-6.1 11.1-7.9 16.9-4.3zM280 276V12c0-6.6-5.4-12-12-12h-24c-6.6 0-12 5.4-12 12v264c0 6.6 5.4 12 12 12h24c6.6 0 12-5.4 12-12z"></path></svg> ${this.data.localization.log_out}
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        `;
    }

    getControlsHtml () {
        return this.data.controls.reduce((html, control) => {
            return html + `
                <li>
                    <a href="${control.uri}">
                    ${this.svgIcons[control.icon]} ${control.text}
                    </a>
                </li>
            `;
        }, '');
    }

    getStatusHtml () {
        return this.data.statuses.reduce((html, control) => {
            return html + `<li class="_cms-status _cms-status-${control.level}">${control.text}</li>`;
        }, '');
    }

    getMaintenanceModeLabelHtml () {
        if (!this.data.isMaintenance) {
            return '';
        }

        let link = '';
        if (this.canTurnOffMaintenance()) {
            link = `
                <a href="#" id="_cms-turn-off-maintenance" title="${this.data.localization.turn_off_maintenance_mode}">  
                    <svg width="10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M207.6 256l107.72-107.72c6.23-6.23 6.23-16.34 0-22.58l-25.03-25.03c-6.23-6.23-16.34-6.23-22.58 0L160 208.4 52.28 100.68c-6.23-6.23-16.34-6.23-22.58 0L4.68 125.7c-6.23 6.23-6.23 16.34 0 22.58L112.4 256 4.68 363.72c-6.23 6.23-6.23 16.34 0 22.58l25.03 25.03c6.23 6.23 16.34 6.23 22.58 0L160 303.6l107.72 107.72c6.23 6.23 16.34 6.23 22.58 0l25.03-25.03c6.23-6.23 6.23-16.34 0-22.58L207.6 256z"></path></svg>
                </a>
            `;
        }

        return `
            <li>
                <span class="maintenance-mode-label">
                    ${this.data.localization.maintenance_mode}
                    ${link}
                </span>
            </li>
        `;
    }

    initializeEvents () {
        // Dropdown
        const toggle = document.getElementById('_cms-user-info');
        toggle.addEventListener('click', function (event) {
            event.stopPropagation();
            toggle.nextElementSibling.classList.toggle("open");
        });
        document.addEventListener('click', function () {
            toggle.nextElementSibling.classList.remove("open");
        });

        // Logout
        document.getElementById('_cms-logout').addEventListener('click', event => {
            event.preventDefault();
            this.logout();
        });

        if (this.canTurnOffMaintenance()) {
            const maintenanceBtn = document.getElementById('_cms-turn-off-maintenance');

            if (maintenanceBtn) {
                maintenanceBtn.addEventListener('click', event => {
                    event.preventDefault();
                    this.turnOffMaintenance();
                });
            }
        }

        if (this.data.switch) {
            this.initializeSwitchListeners();
        }
    }

    indentFixedElements () {
        const elements = document.body.getElementsByTagName("*");

        for (let i = 0; i < elements.length; i++) {
            const style = window.getComputedStyle(elements[i], null);
            if (style.getPropertyValue('position') === 'fixed' &&
                style.getPropertyValue('bottom') !== '0px' &&
                !elements[i].classList.contains('_cms_toolbar')
            ) {
                const top = parseInt(style.getPropertyValue('top'));
                elements[i].style.setProperty('top', (top + 45) + 'px');
            }
        }
    }

    logout () {
        const xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                location.reload(true);
            }
        };
        xhttp.open("POST", this.data.urls.logout, true);
        xhttp.setRequestHeader("X-CSRF-TOKEN", this.data.csrfToken);
        xhttp.send();
    }

    turnOffMaintenance () {
        const xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                location.reload(true);
            }
        };
        xhttp.open("POST", this.data.urls.turnOffMaintenance, true);
        xhttp.setRequestHeader("X-CSRF-TOKEN", this.data.csrfToken);
        xhttp.send();
    }

    canTurnOffMaintenance () {
        return Boolean(this.data.urls.turnOffMaintenance);
    }

    getSwitchHtml() {
        if (!this.data.switch) {
            return '';
        }

        const buttons = Object.keys(this.data.switch.options).reduce((html, value) => {
            const label = this.data.switch.options[value];
            const btnClass = value === this.data.switch.active ? 'class="_cms-binary-switch--active"' : '';
            return html + `
                <button type="button" data-value="${value}" ${btnClass}>${label}</button>
            `;
        }, '');

        return `
            <li>
                <div id="_cms-binary-switch">
                    ${buttons}
                </div>
           </li>`;
    }

    initializeSwitchListeners() {
        const btnSwitch = document.getElementById('_cms-binary-switch');

        btnSwitch.addEventListener('click', event => {
            if (event.target.nodeName.toLowerCase() !== 'button' ||
                event.target.classList.contains('_cms-binary-switch--active')
            ) {
                return;
            }

            const action = this.data.switch.action;

            if (action && action.type === 'post') {
                const xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function () {
                    if (this.readyState === 4 && this.status === 204) {
                        location.reload();
                    }
                };
                xhttp.open("POST", action.url, true);
                xhttp.setRequestHeader("X-CSRF-TOKEN", this.data.csrfToken);
                xhttp.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
                const data = {};
                data[this.data.switch.name] = event.target.dataset.value;
                xhttp.send(JSON.stringify(data));
            }
        });
    }
};
