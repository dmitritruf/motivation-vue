<template>
    <div class="w-60 m-auto">
        <b-form class="search-bar">
            <b-form-input v-model="data.userSearch" type="search" :placeholder="$t('search-user')" aria-label="Search user" />
            <b-button type="submit" @click="searchUser">{{ $t('search') }}</b-button>
        </b-form>
        <br />
        <h3>{{ $t('search-results') }}:</h3>
        <div v-if="searchResults">
            <div  v-for="user in searchResults" :key="user.id">
                <router-link :to="{ name: 'profile', params: { id: user.id}}">{{user.username}}</router-link>
            </div>
        </div>
        <div v-else>{{ $t('no-results') }}</div>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';
export default {
    data() {
        return {
            data: {
                userSearch: '',
            },
        }
    },
    methods: {
        searchUser() {
            this.$store.dispatch('user/searchUser', this.data);
            this.data.userSearch = '';
        },
    },
    computed: {
        ...mapGetters({
            searchResults: 'user/getSearchResults',
        }),
    },
};
</script>

<style>
.search-bar{
    display:flex;
    flex-direction:row;
}
</style>