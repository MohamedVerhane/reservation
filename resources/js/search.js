window.hotelSearch = function () {
  return {
    showAdvanced: false,
    loading: false,
    resultsHtml: '',
    paginationHtml: '',
    totalResults: 0,
    debounceTimer: null,

    form: {
      search: '',
      city: '',
      min_price: '',
      max_price: '',
      guests: '',
      room_type: '',
      check_in: '',
      check_out: '',
      amenities: [],
      star_rating: '',
      sort: '',
    },

    init() {
      if (this.hasActiveFilters) {
        this.showAdvanced = true;
      }
    },

    get hasActiveFilters() {
      return (
        this.form.search !== '' ||
        this.form.city !== '' ||
        this.form.min_price !== '' ||
        this.form.max_price !== '' ||
        this.form.guests !== '' ||
        this.form.room_type !== '' ||
        this.form.check_in !== '' ||
        this.form.check_out !== '' ||
        this.form.amenities.length > 0 ||
        this.form.star_rating !== '' ||
        this.form.sort !== ''
      );
    },

    toggleAmenity(id) {
      const idx = this.form.amenities.indexOf(id);
      if (idx === -1) {
        this.form.amenities.push(id);
      } else {
        this.form.amenities.splice(idx, 1);
      }
      this.search();
    },

    removeFilter(key, value) {
      if (key === 'amenities') {
        const idx = this.form.amenities.indexOf(value);
        if (idx !== -1) {
          this.form.amenities.splice(idx, 1);
        }
      } else {
        this.form[key] = '';
      }
      this.search();
    },

    clearAll() {
      this.form = {
        search: '',
        city: '',
        min_price: '',
        max_price: '',
        guests: '',
        room_type: '',
        check_in: '',
        check_out: '',
        amenities: [],
        star_rating: '',
        sort: '',
      };
      this.search();
    },

    buildQuery() {
      const params = new URLSearchParams();
      const f = this.form;

      if (f.search) params.append('search', f.search);
      if (f.city) params.append('city', f.city);
      if (f.min_price) params.append('min_price', f.min_price);
      if (f.max_price) params.append('max_price', f.max_price);
      if (f.guests) params.append('guests', f.guests);
      if (f.room_type) params.append('room_type', f.room_type);
      if (f.check_in) params.append('check_in', f.check_in);
      if (f.check_out) params.append('check_out', f.check_out);
      if (f.star_rating) params.append('star_rating', f.star_rating);
      if (f.sort) params.append('sort', f.sort);

      f.amenities.forEach((id) => params.append('amenities[]', id));

      return params;
    },

    async search() {
      this.loading = true;

      try {
        const params = this.buildQuery();
        const searchEl = document.querySelector('[data-search-url]');
        const ajaxBase = searchEl
          ? searchEl.getAttribute('data-ajax-url')
          : '/search/ajax';

        const url = ajaxBase + '?' + params.toString();
        const response = await fetch(url, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'text/html',
          },
        });

        const data = await response.text();
        this.resultsHtml = data;
        this.totalResults = (data.match(/data-hotel-card/g) || []).length;

        const newUrl =
          '/search' + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', newUrl);

        if (typeof Alpine !== 'undefined') {
          Alpine.nextTick(() => {
            this.updatePaginationFromDOM();
          });
        }
      } catch (error) {
        console.error('Search failed:', error);
      } finally {
        this.loading = false;
      }
    },

    async goToPage(pageUrl) {
      this.loading = true;

      try {
        const response = await fetch(pageUrl, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'text/html',
          },
        });

        const data = await response.text();
        this.resultsHtml = data;

        const fullUrl = new URL(pageUrl, window.location.origin);
        window.history.pushState({}, '', fullUrl.pathname + fullUrl.search);

        if (typeof Alpine !== 'undefined') {
          Alpine.nextTick(() => {
            this.updatePaginationFromDOM();
          });
        }
      } catch (error) {
        console.error('Pagination failed:', error);
      } finally {
        this.loading = false;
      }
    },

    updatePaginationFromDOM() {
      const container = document.querySelector('[data-pagination]');
      if (container) {
        this.paginationHtml = container.innerHTML;
      }
    },

    debounceSearch() {
      clearTimeout(this.debounceTimer);
      this.debounceTimer = setTimeout(() => {
        this.search();
      }, 300);
    },
  };
};
