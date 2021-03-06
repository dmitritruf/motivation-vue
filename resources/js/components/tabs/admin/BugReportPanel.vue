<template>
    <div>
        <h3>{{ $t('bug-report-panel-title') }}</h3>

        <b-table
            :items="bugReports"
            :fields="bugSortables"
            :sort-by.sync="currentSort"
            :sort-desc.sync="currentSortDesc"
            hover small responsive
            class="font-sm">
            <template #cell(severity)="data">
                <span class="severity">{{ data.item.severity }}</span>
            </template>
            <template #cell(status)="data">
                {{ parseStatus(data.item.status) }}
            </template>
            <template #cell(actions)="data">
                <b-icon-pencil-square 
                    class="icon medium"
                    @click="editBugReport(data.item)" /> 
                <b-icon-envelope class="icon medium" @click="sendMessageToBugReportAuthor(data.item.user_id)" /> 
            </template>
        </b-table>

        <b-modal id="edit-bug-report" hide-footer :title="$t('edit-bug-report')">
            <edit-bug-report :bugReport="bugReportToEdit" @close="closeEditBugReport"/>
        </b-modal>
        <b-modal id="send-message-to-bug-report-author" hide-footer :title="$t('send-message-to-bug-report-author')">
            <send-message :user="bugReportAuthor" @close="closeSendMessageToBugReportAuthor"/>
        </b-modal>

    </div>
</template>


<script>
import {BUG_SORTABLES, BUG_DEFAULTS, BUG_STATUS} from '../../../constants/bugConstants';
import {mapGetters} from 'vuex';
import EditBugReport from '../../modals/EditBugReport.vue';
import SendMessage from '../../modals/SendMessage.vue';
export default {
    components: {
        EditBugReport,
        SendMessage,
    },
    computed: {
        ...mapGetters({
            bugReports: 'bugReport/getBugReports',
        }),
    },
    data() {
        return {
            currentSort: BUG_DEFAULTS.currentSort,
            bugSortables: BUG_SORTABLES,
            currentSortDesc: true,
            bugReportToEdit: null,
            bugReportAuthor: null,
        }
    },    
    methods: {
        sendMessageToBugReportAuthor(authorId) {
            this.$store.dispatch('clearErrors');
            this.bugReportAuthor = {id: authorId};
            this.$bvModal.show('send-message-to-bug-report-author');
        },
        closeSendMessageToBugReportAuthor() {
            this.$bvModal.hide('send-message-to-bug-report-author');
        },
        editBugReport(bugReport) {
            this.$store.dispatch('clearErrors');
            this.bugReportToEdit = bugReport;
            this.$bvModal.show('edit-bug-report');
        },
        closeEditBugReport() {
            this.$bvModal.hide('edit-bug-report');
        },
        parseStatus(status) {
            return BUG_STATUS.find(element => element.value == status).text;
        },
    },
}
</script>
