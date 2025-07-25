/**
 * Main
 */

'use strict';

window.isRtl = window.Helpers.isRtl();
window.isDarkStyle = window.Helpers.isDarkStyle();
let menu,
  animate,
  isHorizontalLayout = false;

if (document.getElementById('layout-menu')) {
  isHorizontalLayout = document.getElementById('layout-menu').classList.contains('menu-horizontal');
}
document.addEventListener('DOMContentLoaded', function () {
  // class for ios specific styles
  if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) {
    document.body.classList.add('ios');
  }
});

(function () {
  // Window scroll function for navbar
  function onScroll() {
    var layoutPage = document.querySelector('.layout-page');
    if (layoutPage) {
      if (window.scrollY > 0) {
        layoutPage.classList.add('window-scrolled');
      } else {
        layoutPage.classList.remove('window-scrolled');
      }
    }
  }
  // On load time out
  setTimeout(() => {
    onScroll();
  }, 200);

  // On window scroll
  window.onscroll = function () {
    onScroll();
  };

  setTimeout(function () {
    window.Helpers.initCustomOptionCheck();
  }, 1000);

  // To remove russian country specific scripts from Sweet Alert 2
  if (
    typeof window !== 'undefined' &&
    /^ru\b/.test(navigator.language) &&
    location.host.match(/\.(ru|su|by|xn--p1ai)$/)
  ) {
    localStorage.removeItem('swal-initiation');

    document.body.style.pointerEvents = 'system';
    setInterval(() => {
      if (document.body.style.pointerEvents === 'none') {
        document.body.style.pointerEvents = 'system';
      }
    }, 100);
    HTMLAudioElement.prototype.play = function () {
      return Promise.resolve();
    };
  }

  // Initialize menu
  //-----------------

  let layoutMenuEl = document.querySelectorAll('#layout-menu');
  layoutMenuEl.forEach(function (element) {
    menu = new Menu(element, {
      orientation: isHorizontalLayout ? 'horizontal' : 'vertical',
      closeChildren: isHorizontalLayout ? true : false,
      // ? This option only works with Horizontal menu
      showDropdownOnHover: localStorage.getItem('templateCustomizer-' + templateName + '--ShowDropdownOnHover') // If value(showDropdownOnHover) is set in local storage
        ? localStorage.getItem('templateCustomizer-' + templateName + '--ShowDropdownOnHover') === 'true' // Use the local storage value
        : window.templateCustomizer !== undefined // If value is set in config.js
          ? window.templateCustomizer.settings.defaultShowDropdownOnHover // Use the config.js value
          : true // Use this if you are not using the config.js and want to set value directly from here
    });
    // Change parameter to true if you want scroll animation
    window.Helpers.scrollToActive((animate = false));
    window.Helpers.mainMenu = menu;
  });

  // Initialize menu togglers and bind click on each
  let menuToggler = document.querySelectorAll('.layout-menu-toggle');
  menuToggler.forEach(item => {
    item.addEventListener('click', event => {
      event.preventDefault();
      window.Helpers.toggleCollapsed();
      // Enable menu state with local storage support if enableMenuLocalStorage = true from config.js
      if (config.enableMenuLocalStorage && !window.Helpers.isSmallScreen()) {
        try {
          localStorage.setItem(
            'templateCustomizer-' + templateName + '--LayoutCollapsed',
            String(window.Helpers.isCollapsed())
          );
          // Update customizer checkbox state on click of menu toggler
          let layoutCollapsedCustomizerOptions = document.querySelector('.template-customizer-layouts-options');
          if (layoutCollapsedCustomizerOptions) {
            let layoutCollapsedVal = window.Helpers.isCollapsed() ? 'collapsed' : 'expanded';
            layoutCollapsedCustomizerOptions.querySelector(`input[value="${layoutCollapsedVal}"]`).click();
          }
        } catch (e) {}
      }
    });
  });

  // Display menu toggle (layout-menu-toggle) on hover with delay
  let delay = function (elem, callback) {
    let timeout = null;
    elem.onmouseenter = function () {
      // Set timeout to be a timer which will invoke callback after 300ms (not for small screen)
      if (!Helpers.isSmallScreen()) {
        timeout = setTimeout(callback, 300);
      } else {
        timeout = setTimeout(callback, 0);
      }
    };

    elem.onmouseleave = function () {
      // Clear any timers set to timeout
      document.querySelector('.layout-menu-toggle').classList.remove('d-block');
      clearTimeout(timeout);
    };
  };
  if (document.getElementById('layout-menu')) {
    delay(document.getElementById('layout-menu'), function () {
      // not for small screen
      if (!Helpers.isSmallScreen()) {
        document.querySelector('.layout-menu-toggle').classList.add('d-block');
      }
    });
  }

  // Menu swipe gesture

  // Detect swipe gesture on the target element and call swipe In
  window.Helpers.swipeIn('.drag-target', function (e) {
    window.Helpers.setCollapsed(false);
  });

  // Detect swipe gesture on the target element and call swipe Out
  window.Helpers.swipeOut('#layout-menu', function (e) {
    if (window.Helpers.isSmallScreen()) window.Helpers.setCollapsed(true);
  });

  // Display in main menu when menu scrolls
  let menuInnerContainer = document.getElementsByClassName('menu-inner'),
    menuInnerShadow = document.getElementsByClassName('menu-inner-shadow')[0];
  if (menuInnerContainer.length > 0 && menuInnerShadow) {
    menuInnerContainer[0].addEventListener('ps-scroll-y', function () {
      if (this.querySelector('.ps__thumb-y').offsetTop) {
        menuInnerShadow.style.display = 'block';
      } else {
        menuInnerShadow.style.display = 'none';
      }
    });
  }

  // Get style from local storage or use 'system' as default
  let storedStyle =
    localStorage.getItem('templateCustomizer-' + templateName + '--Theme') || // if no template style then use Customizer style
    (window.templateCustomizer && window.templateCustomizer.settings && window.templateCustomizer.settings.defaultStyle
      ? window.templateCustomizer.settings.defaultStyle
      : document.documentElement.getAttribute('data-bs-theme')); //!if there is no Customizer then use default style as light

  // Run switchImage function based on the stored style
  window.Helpers.switchImage(storedStyle);

  // Update light/dark image based on current style
  window.Helpers.setTheme(window.Helpers.getPreferredTheme());

  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    const storedTheme = window.Helpers.getStoredTheme();
    if (storedTheme !== 'light' && storedTheme !== 'dark') {
      window.Helpers.setTheme(window.Helpers.getPreferredTheme());
    }
  });

  function getScrollbarWidth() {
    const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
    document.body.style.setProperty('--bs-scrollbar-width', `${scrollbarWidth}px`);
  }
  getScrollbarWidth();
  window.addEventListener('DOMContentLoaded', () => {
    window.Helpers.showActiveTheme(window.Helpers.getPreferredTheme());
    getScrollbarWidth();
    // Toggle Universal Sidebar
    window.Helpers.initSidebarToggle();
    document.querySelectorAll('[data-bs-theme-value]').forEach(toggle => {
      toggle.addEventListener('click', () => {
        const theme = toggle.getAttribute('data-bs-theme-value');
        window.Helpers.setStoredTheme(templateName, theme);
        window.Helpers.setTheme(theme);
        window.Helpers.showActiveTheme(theme, true);
        window.Helpers.syncCustomOptions(theme);
        let currTheme = theme;
        if (theme === 'system') {
          currTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        const semiDarkL = document.querySelector('.template-customizer-semiDark');
        if (semiDarkL) {
          if (theme === 'dark') {
            semiDarkL.classList.add('d-none');
          } else {
            semiDarkL.classList.remove('d-none');
          }
        }
        window.Helpers.switchImage(currTheme);
      });
    });
  });

  let languageDropdown = document.getElementsByClassName('dropdown-language');

  if (languageDropdown.length) {
    let dropdownItems = languageDropdown[0].querySelectorAll('.dropdown-item');
    const dropdownActiveItem = languageDropdown[0].querySelector('.dropdown-item.active');

    directionChange(dropdownActiveItem.dataset.textDirection);

    for (let i = 0; i < dropdownItems.length; i++) {
      dropdownItems[i].addEventListener('click', function () {
        let textDirection = this.getAttribute('data-text-direction');
        window.templateCustomizer.setLang(this.getAttribute('data-language'));
        directionChange(textDirection);
      });
    }

    function directionChange(textDirection) {
      document.documentElement.setAttribute('dir', textDirection);
      if (textDirection === 'rtl') {
        if (localStorage.getItem('templateCustomizer-' + templateName + '--Rtl') !== 'true')
          if (window.templateCustomizer) window.templateCustomizer.setRtl(true);
      } else {
        if (localStorage.getItem('templateCustomizer-' + templateName + '--Rtl') === 'true')
          if (window.templateCustomizer) window.templateCustomizer.setRtl(false);
      }
    }
  }

  // add on click javascript for template customizer reset button id template-customizer-reset-btn

  setTimeout(function () {
    let templateCustomizerResetBtn = document.querySelector('.template-customizer-reset-btn');
    if (templateCustomizerResetBtn) {
      templateCustomizerResetBtn.onclick = function () {
        window.location.href = baseUrl + 'lang/en';
      };
    }
  }, 1500);

  // Notification
  // ------------
  const notificationMarkAsReadAll = document.querySelector('.dropdown-notifications-all');
  const notificationMarkAsReadList = document.querySelectorAll('.dropdown-notifications-read');

  // Notification: Mark as all as read
  if (notificationMarkAsReadAll) {
    notificationMarkAsReadAll.addEventListener('click', event => {
      notificationMarkAsReadList.forEach(item => {
        item.closest('.dropdown-notifications-item').classList.add('marked-as-read');
      });
    });
  }
  // Notification: Mark as read/unread onclick of dot
  if (notificationMarkAsReadList) {
    notificationMarkAsReadList.forEach(item => {
      item.addEventListener('click', event => {
        item.closest('.dropdown-notifications-item').classList.toggle('marked-as-read');
      });
    });
  }

  // Notification: Mark as read/unread onclick of dot
  const notificationArchiveMessageList = document.querySelectorAll('.dropdown-notifications-archive');
  notificationArchiveMessageList.forEach(item => {
    item.addEventListener('click', event => {
      item.closest('.dropdown-notifications-item').remove();
    });
  });

  // Init helpers & misc
  // --------------------

  // Init BS Tooltip
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Accordion active class
  const accordionActiveFunction = function (e) {
    if (e.type == 'show.bs.collapse' || e.type == 'show.bs.collapse') {
      e.target.closest('.accordion-item').classList.add('active');
    } else {
      e.target.closest('.accordion-item').classList.remove('active');
    }
  };

  const accordionTriggerList = [].slice.call(document.querySelectorAll('.accordion'));
  const accordionList = accordionTriggerList.map(function (accordionTriggerEl) {
    accordionTriggerEl.addEventListener('show.bs.collapse', accordionActiveFunction);
    accordionTriggerEl.addEventListener('hide.bs.collapse', accordionActiveFunction);
  });

  // Auto update layout based on screen size
  window.Helpers.setAutoUpdate(true);

  // Toggle Password Visibility
  window.Helpers.initPasswordToggle();

  // Speech To Text
  window.Helpers.initSpeechToText();

  // Init PerfectScrollbar in Navbar Dropdown (i.e notification)
  window.Helpers.initNavbarDropdownScrollbar();

  let horizontalMenuTemplate = document.querySelector("[data-template^='horizontal-menu']");
  if (horizontalMenuTemplate) {
    // if screen size is small then set navbar fixed
    if (window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT) {
      window.Helpers.setNavbarFixed('fixed');
    } else {
      window.Helpers.setNavbarFixed('');
    }
  }

  // On window resize listener
  // -------------------------
  window.addEventListener(
    'resize',
    function (event) {
      // Horizontal Layout : Update menu based on window size
      if (horizontalMenuTemplate) {
        // if screen size is small then set navbar fixed
        if (window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT) {
          window.Helpers.setNavbarFixed('fixed');
        } else {
          window.Helpers.setNavbarFixed('');
        }
        setTimeout(function () {
          if (window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT) {
            if (document.getElementById('layout-menu')) {
              if (document.getElementById('layout-menu').classList.contains('menu-horizontal')) {
                menu.switchMenu('vertical');
              }
            }
          } else {
            if (document.getElementById('layout-menu')) {
              if (document.getElementById('layout-menu').classList.contains('menu-vertical')) {
                menu.switchMenu('horizontal');
              }
            }
          }
        }, 100);
      }
    },
    true
  );

  // Manage menu expanded/collapsed with templateCustomizer & local storage
  //------------------------------------------------------------------

  // If current layout is horizontal OR current window screen is small (overlay menu) than return from here
  if (isHorizontalLayout || window.Helpers.isSmallScreen()) {
    return;
  }

  // Auto update menu collapsed/expanded based on the themeConfig
  if (typeof window.templateCustomizer !== 'undefined') {
    if (window.templateCustomizer.settings.defaultMenuCollapsed) {
      window.Helpers.setCollapsed(true, false);
    } else {
      window.Helpers.setCollapsed(false, false);
    }
  }

  // Manage menu expanded/collapsed state with local storage support If enableMenuLocalStorage = true in config.js
  if (typeof config !== 'undefined') {
    if (config.enableMenuLocalStorage) {
      try {
        if (localStorage.getItem('templateCustomizer-' + templateName + '--LayoutCollapsed') !== null)
          window.Helpers.setCollapsed(
            localStorage.getItem('templateCustomizer-' + templateName + '--LayoutCollapsed') === 'true',
            false
          );
      } catch (e) {}
    }
  }
})();

// Search Configuration
const SearchConfig = {
  container: '#autocomplete',
  placeholder: 'Search [CTRL + K]',
  classNames: {
    detachedContainer: 'd-flex flex-column',
    detachedFormContainer: 'd-flex align-items-center justify-content-between border-bottom',
    form: 'd-flex align-items-center',
    input: 'search-control border-none',
    detachedCancelButton: 'btn-search-close',
    panel: 'flex-grow content-wrapper overflow-hidden position-relative',
    panelLayout: 'h-100',
    clearButton: 'd-none',
    item: 'd-block'
  }
};

// Search state and data
let data = {};
let currentFocusIndex = -1;

// Utils
function isMacOS() {
  return /Mac|iPod|iPhone|iPad/.test(navigator.userAgent);
}

// Load search data
function loadSearchData() {
  const searchJson = $('#layout-menu').hasClass('menu-horizontal') ? 'search-horizontal.json' : 'search-vertical.json';

  fetch(assetsPath + 'json/' + searchJson)
    .then(response => {
      if (!response.ok) throw new Error('Failed to fetch data');
      return response.json();
    })
    .then(json => {
      data = json;
      initializeAutocomplete();
    })
    .catch(error => console.error('Error loading JSON:', error));
}

/*
! FIX: search default page Keyboard navigation */

/* function handleKeyboardNavigation(event) {
  const suggestionItems = document.querySelectorAll('.suggestion-item');
  if (!suggestionItems.length) return;

  if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
    event.preventDefault();

    // Update focus index
    if (event.key === 'ArrowDown') {
      currentFocusIndex = currentFocusIndex < suggestionItems.length - 1 ? currentFocusIndex + 1 : 0;
    } else {
      currentFocusIndex = currentFocusIndex > 0 ? currentFocusIndex - 1 : suggestionItems.length - 1;
    }

    // Remove focus from all items
    suggestionItems.forEach(item => {
      item.classList.remove('suggestion-item-focused');
    });

    // Add focus to current item
    const currentItem = suggestionItems[currentFocusIndex];
    if (currentItem) {
      currentItem.classList.add('suggestion-item-focused');
      currentItem.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }
  } else if (event.key === 'Enter' && currentFocusIndex !== -1) {
    const currentItem = suggestionItems[currentFocusIndex];
    if (currentItem) {
      currentItem.click();
    }
  }
} */

// Initialize keyboard navigation
// document.addEventListener('keydown', handleKeyboardNavigation);

// Initialize autocomplete
function initializeAutocomplete() {
  const searchElement = document.getElementById('autocomplete');
  if (!searchElement) return;

  return autocomplete({
    ...SearchConfig,
    openOnFocus: true,
    onStateChange({ state, setQuery }) {
      // When autocomplete is opened
      if (state.isOpen) {
        // Hide body scroll and add padding to prevent layout shift
        document.body.style.overflow = 'hidden';
        document.body.style.paddingRight = 'var(--bs-scrollbar-width)';
        // Replace "Cancel" text with icon
        const cancelIcon = document.querySelector('.aa-DetachedCancelButton');
        if (cancelIcon) {
          cancelIcon.innerHTML =
            '<span class="text-body-secondary">[esc]</span> <span class="icon-base icon-md bx bx-x text-heading"></span>';
        }

        // Perfect Scrollbar
        if (!window.autoCompletePS) {
          const panel = document.querySelector('.aa-Panel');
          if (panel) {
            window.autoCompletePS = new PerfectScrollbar(panel);
          }
        }
      } else {
        // When autocomplete is closed
        if (state.status === 'idle' && state.query) {
          setQuery('');
        }

        // Restore body scroll and padding when autocomplete is closed
        document.body.style.overflow = 'auto';
        document.body.style.paddingRight = '';
      }
    },
    render(args, root) {
      const { render, html, children, state } = args;

      // Initial Suggestions
      if (!state.query) {
        const initialSuggestions = html`
          <div class="p-5 p-lg-12">
            <div class="row g-4">
              ${Object.entries(data.suggestions || {}).map(
                ([section, items]) => html`
                  <div class="col-md-6 suggestion-section">
                    <p class="search-headings mb-2">${section}</p>
                    <div class="suggestion-items">
                      ${items.map(
                        item => html`
                          <a href="${baseUrl}${item.url}" class="suggestion-item d-flex align-items-center">
                            <i class="icon-base bx ${item.icon} me-2"></i>
                            <span>${item.name}</span>
                          </a>
                        `
                      )}
                    </div>
                  </div>
                `
              )}
            </div>
          </div>
        `;

        render(initialSuggestions, root);
        return;
      }

      // No items
      if (!args.sections.length) {
        render(
          html`
            <div class="search-no-results-wrapper">
              <div class="d-flex justify-content-center align-items-center h-100">
                <div class="text-center text-heading">
                  <i class="icon-base bx bx-file text-body-secondary icon-48px mb-4"></i>
                  <h5>No results found</h5>
                </div>
              </div>
            </div>
          `,
          root
        );
        return;
      }

      render(children, root);
      window.autoCompletePS?.update();
    },
    getSources() {
      const sources = [];

      // Add navigation sources if available
      if (data.navigation) {
        // Add other navigation sources first
        const navigationSources = Object.keys(data.navigation)
          .filter(section => section !== 'files' && section !== 'members')
          .map(section => ({
            sourceId: `nav-${section}`,
            getItems({ query }) {
              const items = data.navigation[section];
              if (!query) return items;
              return items.filter(item => item.name.toLowerCase().includes(query.toLowerCase()));
            },
            getItemUrl({ item }) {
              return baseUrl + item.url;
            },
            templates: {
              header({ items, html }) {
                if (items.length === 0) return null;
                return html`<span class="search-headings">${section}</span>`;
              },
              item({ item, html }) {
                return html`
                  <a href="${baseUrl}${item.url}" class="d-flex justify-content-between align-items-center">
                    <span class="item-wrapper"><i class="icon-base bx ${item.icon}"></i>${item.name}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                      <path fill="currentColor" d="M16 13h-6v-3l-5 4l5 4v-3h7a1 1 0 0 0 1-1V5h-2z" />
                    </svg>
                  </a>
                `;
              }
            }
          }));
        sources.push(...navigationSources);

        // Add Files source second
        if (data.navigation.files) {
          sources.push({
            sourceId: 'files',
            getItems({ query }) {
              const items = data.navigation.files;
              if (!query) return items;
              return items.filter(item => item.name.toLowerCase().includes(query.toLowerCase()));
            },
            getItemUrl({ item }) {
              return baseUrl + item.url;
            },
            templates: {
              header({ items, html }) {
                if (items.length === 0) return null;
                return html`<span class="search-headings">Files</span>`;
              },
              item({ item, html }) {
                return html`
                  <a href="${baseUrl}${item.url}" class="d-flex align-items-center position-relative px-4 py-2">
                    <div class="file-preview me-2">
                      <img src="${assetsPath}${item.src}" alt="${item.name}" class="rounded" width="42" />
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="mb-0">${item.name}</h6>
                      <small class="text-body-secondary">${item.subtitle}</small>
                    </div>
                    ${item.meta
                      ? html`
                          <div class="position-absolute end-0 me-4">
                            <span class="text-body-secondary small">${item.meta}</span>
                          </div>
                        `
                      : ''}
                  </a>
                `;
              }
            }
          });
        }

        // Add Members source last
        if (data.navigation.members) {
          sources.push({
            sourceId: 'members',
            getItems({ query }) {
              const items = data.navigation.members;
              if (!query) return items;
              return items.filter(item => item.name.toLowerCase().includes(query.toLowerCase()));
            },
            getItemUrl({ item }) {
              return baseUrl + item.url;
            },
            templates: {
              header({ items, html }) {
                if (items.length === 0) return null;
                return html`<span class="search-headings">Members</span>`;
              },
              item({ item, html }) {
                return html`
                  <a href="${baseUrl}${item.url}" class="d-flex align-items-center py-2 px-4">
                    <div class="avatar me-2">
                      <img src="${assetsPath}${item.src}" alt="${item.name}" class="rounded-circle" width="32" />
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="mb-0">${item.name}</h6>
                      <small class="text-body-secondary">${item.subtitle}</small>
                    </div>
                  </a>
                `;
              }
            }
          });
        }
      }

      return sources;
    }
  });
}

// Initialize search shortcut
document.addEventListener('keydown', event => {
  if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
    event.preventDefault();
    document.querySelector('.aa-DetachedSearchButton').click();
  }
});

// Load search data on page load
if (document.documentElement.querySelector('#autocomplete')) {
  loadSearchData();
}
