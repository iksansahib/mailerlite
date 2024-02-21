<template>
  <div>
  <div>Name: <input :value="name" @input="event => name = event.target.value"></div>
  <div>Last Name: <input :value="lastName" @input="event => lastName = event.target.value"></div>
  <div>Email: <input :value="email" @input="event => email = event.target.value"></div>
  <div>Status: <input :value="status" @input="event => status = event.target.value"></div>
  <div><button @click="submit()">Submit</button></div>
  <br />
  <table border="1" width="700">
    <thead>
      <th>Name</th>
      <th>Email</th>
      <th>Last Name</th>
      <th>Status</th>
    </thead>
    <tbody>
      <tr align="center" v-for="item in data" v-bind:key="item.id">
        <td>
          {{item.name}}
        </td>
        <td>
          {{item.last_name}}
        </td>
        <td>
          {{item.email}}
        </td>
        <td>
          {{item.status}}
        </td>
      </tr>
    </tbody>
    <tfoot>
      <tr><td colspan="4">
        <div class="pagination">
          <a href="?page=1">&#9664;&#9664;</a><a :href="'?page=' + previousPage">&#9664;</a>
          <!-- FIXME implement cursor pagination?? -->
          <a :href="'?page=' + page" v-for="page in pages" v-bind:key="page">{{ page }}</a>
          <a :href="'?page=' + nextPage">&#9654;</a><a :href="'?page=' + lastPage">&#9654;&#9654;</a>
        </div>

      </td></tr>
    </tfoot>
  </table>
</div>
</template>

<style>
@media (min-width: 1024px) {
  .about {
    min-height: 100vh;
    display: flex;
    align-items: center;
  }
}
.pagination {
  display: inline-block;
}

.pagination a {
  color: rgb(15, 145, 19);
  float: left;
  padding: 8px 16px;
  text-decoration: none;
}
</style>

<script>
import axios from "axios";
export default {
  el: '#app',
  data () {
    return {
      data: null,
      total: 0,
      page: 1,
      size: 10,
      pages: [],
      previousPage: 1,
      nextPage: 2,
      totalPage: 0,
      lastPage: 0,
      input: {
        name: null,
        lastName: null,
        email: null,
        status: null,
      }
    }
  },
  filters: {
    currencydecimal (value) {
      return value.toFixed(2)
    }
  },
  mounted () {
    this.page = parseInt(this.$route.query.page ?? 1);
    axios
      .get('http://localhost:8099/index.php/subscribers/list?page=' + this.page + '&size=' +this.size)
      .then(response => {
        this.data = response.data.data
        this.total = response.data.total
        this.lastPage = Math.ceil(this.total / this.size);
        this.previousPage = this.page - 1 <= 0 ? this.page : this.page - 1
        this.nextPage = this.page + 1 >= this.lastPage ? this.lastPage : this.page + 1
        for(let i = 1;i <= this.lastPage; i++) {
          this.pages.push(i);
        }
      })
      .catch(error => {
        console.log(error)
        this.errored = true
      })
      .finally(() => this.loading = false)
  },
  methods: {
    async submit() {
      await axios.post('http://localhost:8099/index.php/subscribers', {
        name: this.name,
        last_name: this.lastName,
        email: this.email,
        status: this.status
      })
      this.$router.go();
    }
  }
};
</script>
