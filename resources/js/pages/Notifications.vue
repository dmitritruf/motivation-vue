<template>
    <div>
        <h2>{{ $t('notifications') }}</h2>
        <notification-block v-for="notification in notifications" :key="notification.id" :notification="notification" />
    </div>
</template>


<script>
import {mapGetters} from 'vuex';
import NotificationBlock from '../components/small/NotificationBlock.vue';

export default {
    components: {NotificationBlock},
    mounted() {
        this.$store.dispatch('notification/getNotifications');
    },
    computed: {
        ...mapGetters({
            notifications: 'notification/getNotifications',
        }),
    },
}
</script>