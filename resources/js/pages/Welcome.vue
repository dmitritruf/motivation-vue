<template>
    <div>
        <b-modal id="first-modal" hide-footer hide-header-close no-close-on-backdrop no-close-on-esc>
            <template #modal-title>
                <h2>{{ $t('welcome') }}</h2>
                <p class="silent mb-0">{{ $t('not-yet-done') }}</p>
            </template>
            <div>
                <b-form-group
                    :label="$t('rewards-type')"
                    label-for="rewards-type">
                    <b-form-text class="text-muted mb-2">{{ $t('which-reward-type') }}</b-form-text>
                    <b-form-radio-group :checked="user.rewardsType">
                        <b-form-radio v-model="user.rewardsType" type="radio" 
                                      class="input-override" value="NONE" name="rewards-type">
                            <p class="radio-label">{{ $t('no-rewards') }}</p>
                        </b-form-radio>
                        <b-form-radio v-model="user.rewardsType" type="radio" 
                                      class="input-override" value="CHARACTER" name="rewards-type">
                            <p class="radio-label">{{ $t('character-reward') }}</p>
                        </b-form-radio>
                        <b-form-radio v-model="user.rewardsType" type="radio" 
                                      class="input-override" value="VILLAGE" name="rewards-type">
                            <p class="radio-label">{{ $t('village-reward') }}</p>
                        </b-form-radio>
                    </b-form-radio-group>
                    <base-form-error name="rewards-type" /> 
                </b-form-group>
                <b-form-group v-if="user.rewardsType == 'CHARACTER' || user.rewardsType == 'VILLAGE'"
                              :label="parsedLabelName"
                              label-for="reward_object_name"
                              :description="$t('change-name-later')">
                    <b-form-input 
                        id="reward_object_name" 
                        v-model="user.reward_object_name"
                        type="text" 
                        name="reward_object_name" 
                        :placeholder="parsedLabelName"  />
                    <base-form-error name="reward-object_name" /> 
                </b-form-group>
                <b-button block @click="nextModal()">{{ $t('next') }}</b-button>
                <b-button block variant="danger" @click="logout()">{{ $t('logout')}}</b-button>
            </div>
        </b-modal>
        <b-modal id="second-modal" hide-footer hide-header-close no-close-on-backdrop no-close-on-esc>
            <template #modal-title>
                <h2>{{ $t('little-more') }}</h2>
                <p class="silent mb-0">{{ $t('pick-example-tasks') }}</p>
            </template>
            <div>
                <b-form-group
                    :label="$t('example-tasks')"
                    label-for="example-tasks">
                    <div class="examples-slot">
                        <b-form-checkbox 
                            v-for="task in exampleTasks"
                            :key="task.id"
                            v-model="user.tasks"
                            :value="task.id" 
                            name="example-tasks">
                            <p class="task-title d-flex label-override">
                                {{task.name}}
                            </p>
                            <p class="task-description label-override">{{task.description}}</p>
                        </b-form-checkbox>
                    </div>
                    
                </b-form-group>
                <div class="d-flex">
                    <b-button class="mr-1" @click="startFirstModal()">{{ $t('go-back') }}</b-button>
                    <b-button @click="confirmSettings()">{{ $t('submit') }}</b-button>
                    <b-button class="ml-auto" variant="danger" @click="logout()">{{ $t('logout')}}</b-button>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
import BaseFormError from '../components/BaseFormError.vue';
import {mapGetters} from 'vuex';
export default {
    components: {BaseFormError},
    mounted () {
        this.$store.dispatch('clearErrors');
        this.$store.dispatch('task/fetchExampleTasks');
        this.startFirstModal();
    },
    data() {
        return {
            user: {
                rewardsType: 'NONE',
                tasks: [],
                reward_object_name: null,
            },
        }
    },
    methods: {
        nextModal() {
            if (this.checkInput()) {
                this.$bvModal.hide('first-modal');
                this.$bvModal.show('second-modal');
            }
        },
        startFirstModal() {
            this.$bvModal.show('first-modal');
            this.$bvModal.hide('second-modal');
        },
        confirmSettings() {
            this.$store.dispatch('user/confirmRegister', this.user);
        },
        checkInput() {
            if (this.user.rewardsType == 'CHARACTER' && !this.user.reward_object_name) {
                this.$store.commit('setErrorMessages', {'reward_object_name': ['No character name given.']});
                return false;
            } else if (this.user.rewardsType == 'VILLAGE' && !this.user.reward_object_name) {
                this.$store.commit('setErrorMessages', {'reward_object_name': ['No village name given.']});
            } else {
                this.$store.dispatch('clearErrors');
                return true;
            }
        },
        logout() {
            this.$store.dispatch('user/logout');
        },
    },
    computed: {
        ...mapGetters({
            exampleTasks: 'task/getExampleTasks',
        }),
        parsedLabelName() {
            if (this.user.rewardsType == 'CHARACTER') {
                return this.$t('character-name');
            } else if (this.user.rewardsType == 'VILLAGE') {
                return this.$t('village-name');
            } else {
                return null;
            }
        },
    },
}
</script>

<style>
.examples-slot{
    max-height:500px;
    overflow-y:scroll;
}
</style>