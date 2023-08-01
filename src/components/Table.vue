<template>
<b-overlay :show="loading" rounded="sm">

  <b-pagination
          v-model="pagination.pageNumber"
          :total-rows="pagination.totalCount"
          :per-page="pagination.pageSize"
          @change="loadPage"
  ></b-pagination>

  <table class="table table-condensed table-striped">
    <thead>
      <tr>
        <th><a href="#" alt="Ид">Ид</a></th>
        <th><a href="#" alt="Ид">Категория</a></th>
        <th><a href="#" alt="Название">Название</a></th>
        <th><a href="#" alt="Цена">Цена</a></th>
      </tr>
    </thead>
    <tbody v-if="!loading">
      <tr class="" v-for="(item, index) in items">
        <td>
            {{ item.id }}
        </td>
        <td>
            {{ item.category.name }}
        </td>
        <td>
            {{ item.name }}
        </td>
        <td>
            {{ item.price }}
        </td>
      </tr>
    </tbody>
  </table>

  <b-pagination
    v-model="pagination.pageNumber"
    :total-rows="pagination.totalCount"
    :per-page="pagination.pageSize"
    @change="loadPage"
  ></b-pagination>

</b-overlay>
</template>

<script>
import('../../node_modules/vuetify/dist/vuetify.min.css');
import('../../node_modules/bootstrap/dist/css/bootstrap.css');
import {API} from "../../src/api/http.js"; 
export default {
  name: 'Table',
  data () {
    return {
      search: '',
      totalItems: 0,
      items: [],
      loading: true,
      pagination: {
        totalCount: 0,
        pageNumber: 1,
        pageCount: 0,
        pageSize: 20,
      },
      headers: [
        {
          text: 'Ид',
          align: 'right',
          sortable: false,
          value: 'id'
        },
        { text: 'Название', value: 'name' },
      ],
      search: {
        text: '',
      },
    }
  },
  mounted () {
    this.fetchData()
  },
  methods: {
    fetchData(page = 1) {
      this.loading = true;
      let productUrl = 'product';
      let params = {
        page: page,
        'per-page': this.pagination.pageSize,
        sort:this.search.sort,
        's[name]': this.search.text,
      };
      API.get(productUrl, {
        params: params,
      }).then(response => {
        this.loading = false;
        this.items = response.data;
        this.pagination.pageSize = parseInt(response.headers['x-pagination-per-page']);
        this.pagination.totalCount = parseInt(response.headers['x-pagination-total-count']);
        this.pagination.pageCount = parseInt(response.headers['x-pagination-page-count']);
        window.scrollTo(0, 0);
      }).catch(error => {
        this.loading = false;
        this.error = error;
      })
    },
    loadPage(pageNum) {
      this.fetchData(pageNum);
    },
  },
}
</script>