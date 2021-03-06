// @ts-nocheck
import axios from 'axios';

export default {

    namespaced: true,
    
    state: {
        taskLists: {},
    },
    mutations: {
        setTaskLists(state, taskLists) {
            state.taskLists = taskLists;
        },
    },
    getters: {
        getTaskLists: state => {
            return state.taskLists;
        },
    },
    actions: {
        storeTaskList: ({commit, dispatch}, taskList) => {
            return axios.post('/tasklists', taskList).then(response => {
                dispatch('sendToasts', response.data.message, {root:true});
                commit('setTaskLists', response.data.data);
                return Promise.resolve();
            });
        },
        updateTaskList: ({commit, dispatch}, taskList) => {
            return axios.put('/tasklists/' + taskList.id, taskList).then(response => {
                dispatch('sendToasts', response.data.message, {root:true});
                commit('setTaskLists', response.data.data);
                return Promise.resolve();
            });
        },
        deleteTaskList: ({commit, dispatch}, taskList) => {
            axios.delete('/tasklists/' + taskList.id).then(response => {
                dispatch('sendToasts', response.data.message, {root:true});
                commit('setTaskLists', response.data.data);
            });
        },
        mergeTasks: (_, data) => {
            axios.post('/tasks/merge/' + data.taskListId, data);
        },
    },
};
