<template>
<b-overlay :show="loading" rounded="sm">

  <div class="search" v-if="true">
    <div class="d-flex flex-wrap align-items-center">
      <div class="mr-2">
        <b-form-group label="Название">
          <b-form-input type="search" size="sm" v-model="search.text" tabindex="1" @input="fetchData"></b-form-input>
        </b-form-group>
      </div>
      <div class="align-self-center mt-2 mr-2">
        <b-button @click="fetchData" class="btn btn-info ml-1">
          <font-awesome-icon icon="search" class="mr-1" />Поиск
        </b-button>
      </div>

      <div class="mt-2 mr-2" v-if="!loading">
        <p class="m-0" :class="pagination.totalCount === 0 && 'text-danger'">
          Найдено: {{ pagination.totalCount }}
        </p>
      </div>

    </div>
    <hr>
  </div>

  <b-pagination
    v-model="pagination.pageNumber"
    :total-rows="pagination.totalCount"
    :per-page="pagination.pageSize"
    @change="loadPage"
  ></b-pagination>

  <table class="table table-condensed table-striped">
    <thead>
      <tr>
        <th></th>
        <th><a href="#" alt="Ид">Ид</a></th>
        <th><a href="#" alt="Ид">Категория</a></th>
        <th><a href="#" alt="Название">Название</a></th>
        <th><a href="#" alt="Цена">Цена</a></th>
      </tr>
    </thead>
    <tbody v-if="!loading" v-for="(item, index) in items">
      <tr>
        <td>
            <button @click="toggleDescription(index)">
              {{ item.showDescription ? 'Скрыть' : 'Показать' }}
            </button>
        </td>
        <td>
            {{ item.id }}
        </td>
        <td>
            {{ item.category_name }}
        </td>
        <td v-html="highlightSubstring(item.name, search.text)"></td>
        <td>
            {{ item.price }}
        </td>
      </tr>
      <tr v-if="item.showDescription">
        <td colspan="5">
            {{ item.description }}
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
    toggleDescription(index) {
      this.items[index].showDescription = !this.items[index].showDescription;
    },
    highlightSubstring(search, glue) {
        return search.replace(new RegExp(glue, 'gi'), (match) => {
            return '<strong style="color:red">' + match + '</strong>';
        });
    },
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
        this.items = response.data.map(item => {
          return { 
            id:item.id, 
            name:item.name,
            category_name:item.category.name,
            price:item.price,
            description:item.description,
            showDescription: false
          };
        });
        this.pagination.pageSize = parseInt(response.headers['x-pagination-per-page']);
        this.pagination.totalCount = parseInt(response.headers['x-pagination-total-count']);
        this.pagination.pageCount = parseInt(response.headers['x-pagination-page-count']);
        //window.scrollTo(0, 0);
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