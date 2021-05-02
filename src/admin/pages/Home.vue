<template>
  <section>
    <b-table
      :data="data"
      :loading="loading"
      paginated
      backend-pagination
      :total="total"
      :per-page="perPage"
      @page-change="onPageChange"
      aria-next-label="Next page"
      aria-previous-label="Previous page"
      aria-page-label="Page"
      aria-current-label="Current page"
      backend-sorting
      :default-sort-direction="defaultSortOrder"
      :default-sort="[sortField, sortOrder]"
      @sort="onSort"
    >
      <b-table-column
        field="original_title"
        label="Order ID"
        sortable
        v-slot="props"
      >
        #{{ props.row.order_id }}
      </b-table-column>

      <b-table-column
        field="first_name"
        label="First Name"
        sortable
        v-slot="props"
      >
        {{ props.row.customer_first_name }}
      </b-table-column>

      <b-table-column
        field="last_name"
        label="Last Name"
        sortable
        v-slot="props"
      >
        {{ props.row.customer_last_name }}
      </b-table-column>

      <b-table-column field="first_name" label="Email" sortable v-slot="props">
        {{ props.row.customer_email }}
      </b-table-column>

      <b-table-column
        field="vote_average"
        label="Customer Status"
        numeric
        sortable
        v-slot="props"
      >
        <span class="tag" v-if="props.row.customer_user_id != null">
          <b-tag type="is-light">Registered</b-tag>
        </span>
        <span class="tag" v-if="props.row.customer_user_id == null">
          <b-tag type="is-light">Unregistered</b-tag>
        </span>
      </b-table-column>

      <b-table-column
        field="release_date"
        label="Serial Registered"
        sortable
        centered
        v-slot="props"
      >
        {{ props.row.registered_at ? props.row.registered_at : "" }}
      </b-table-column>

      <b-table-column
        field="vote_average"
        label="Warranty Status"
        sortable
        v-slot="props"
      >
        <span class="tag" v-if="props.row.order_serial == null">
          <b-taglist attached>
            <b-tag type="is-danger">Unclaimed</b-tag>
            <b-tag type="is-danger is-light">No Serial Registered</b-tag>

          </b-taglist>
  
        </span>
        <span class="tag" v-else-if="props.row.claimed_at == null">
          <b-taglist attached>
            <b-tag type="is-warning">Unclaimed</b-tag>
            <b-tag type="is-warning is-light"
              >Current Serial: {{ props.row.order_serial }}</b-tag
            >
          </b-taglist>
        </span>
        <span class="tag" v-else-if="props.row.claimed_at != null">
          <b-taglist attached>
            <b-tag type="is-success">Claimed</b-tag>
            <b-tag type="is-success is-light"
              >Current Serial: {{ props.row.order_serial }}</b-tag
            >
          </b-taglist>
        </span>
      </b-table-column>

      <b-table-column label="Address" width="200" v-slot="props">
             <b-tooltip :label="props.row.customer_address"
            type="is-dark"
            position="is-bottom">
        {{ props.row.customer_address | truncate(20) }}
        </b-tooltip>
      </b-table-column>
    </b-table>
      <b-button
      label="Learn about this table 📦"
      type="is-primary"
      size="large"
      @click="alert"
    />
  </section>
</template>

<script>
import Vue from "vue";
import Buefy from "buefy";
import "buefy/dist/buefy.css";
import axios from "axios";
import VueAxios from "vue-axios";

Vue.use(Buefy);
export default {
  data() {
    return {
      isActive: false,
      data: [],
      total: 0,
      loading: false,
      sortField: "vote_count",
      sortOrder: "desc",
      defaultSortOrder: "desc",
      page: 1,
      perPage: 20,
    };
  },
  methods: {
    /*
     * Load async data
     */
    alert() {
      this.$buefy.dialog.alert(
        "When a dealer makes an order, their customer is added to the database. This entry will include all the usual identifying information [Name, Email etc...] If the customer email is already in our database the customer status will be marked as registered. If a serial is registered for that order, the serial and registration date will be added to the dealer order entry. <br> When the serial is claimed, the warranty status will update."
      );
    },

    loadAsyncData() {
      const params = [
        "api_key=bb6f51bef07465653c3e553d6ab161a8",
        "language=en-US",
        "include_adult=false",
        "include_video=false",
        `sort_by=${this.sortField}.${this.sortOrder}`,
        `page=${this.page}`,
      ].join("&");

      this.loading = true;
      axios
        .get(
          `https://wordpress-322025-1894271.cloudwaysapps.com/wp-json/dealer/api/orders`
        )
        .then(({ data }) => {
          // api.themoviedb.org manage max 1000 pages
          this.data = [];
          let currentTotal = data.total_results;
          if (data.total_results / this.perPage > 1000) {
            currentTotal = this.perPage * 1000;
          }
          this.total = currentTotal;
          data.results.forEach((item) => {
            item.release_date = item.release_date
              ? item.release_date.replace(/-/g, "/")
              : null;
            this.data.push(item);
          });
          this.loading = false;
        })
        .catch((error) => {
          this.data = [];
          this.total = 0;
          this.loading = false;
          throw error;
        });
    },
    /*
     * Handle page-change event
     */
    onPageChange(page) {
      this.page = page;
      this.loadAsyncData();
    },
    /*
     * Handle sort event
     */
    onSort(field, order) {
      this.sortField = field;
      this.sortOrder = order;
      this.loadAsyncData();
    },
    /*
     * Type style in relation to the value
     */
    type(value) {
      const number = parseFloat(value);
      if (number < 6) {
        return "is-danger";
      } else if (number >= 6 && number < 8) {
        return "is-warning";
      } else if (number >= 8) {
        return "is-success";
      }
    },
  },
  filters: {
    /**
     * Filter to truncate string, accepts a length parameter
     */
    truncate(value, length) {
      return value.length > length ? value.substr(0, length) + "..." : value;
    },
  },
  mounted() {
    this.loadAsyncData();
  },
};
</script>
