<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps(['post']);
const emit = defineEmits(['updated']);

const dialog = ref(null);
const visible = ref(false);
const showDialog = function () {
    dialog.value.showModal();
    visible.value = true;
}
const closeDialog = function () {
    dialog.value.close();
}
const editPost = useForm({
    message: props.post.post,
    visibility: props.post.visibility,
})

function editExistPost() {
    editPost.patch('/post/' + props.post.id + '/edit', {
        preserveScroll: true,
        onError: function(){
            console.log({
                message: editPost.message,
                visibility: editPost.visibility,
            });
        },
        onSuccess: function () {
            closeDialog();
            emit('updated');
            editPost.reset();
        },
    });
}

defineExpose({
    show: showDialog,
    close: closeDialog,
    visible: visible.value,
});
</script>

<template>
    <dialog ref="dialog" @close="visible = false">
        <form @submit.prevent="editExistPost" class="border bg-white p-8 flex flex-col gap-y-4 w-[40rem]">
            <div class="flex flex-row justify-between items-center">
                <div class="flex flex-row gap-x-4">
                    <div class="h-12 w-12 bg-gray-300 rounded-full"></div>
                    <div class="flex flex-col">
                        <p>{{ post.user.name }} <span
                                v-if="post.user.username == $page.props.auth.user.username">(You)</span>
                        </p>
                        <div class="text-sm">
                            <Link as="a" class="underline underline-offset-1" :href="'/profile/' + post.user.username"
                                v-if="post.user.username != $page.props.auth.user.username">
                            @{{ post.user.username }}
                            </Link>
                            <p v-else>@{{ post.user.username }}</p>
                        </div>
                    </div>
                </div>
                <button type="button" @click="closeDialog">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="m4.397 4.554.073-.084a.75.75 0 0 1 .976-.073l.084.073L12 10.939l6.47-6.47a.75.75 0 1 1 1.06 1.061L13.061 12l6.47 6.47a.75.75 0 0 1 .072.976l-.073.084a.75.75 0 0 1-.976.073l-.084-.073L12 13.061l-6.47 6.47a.75.75 0 0 1-1.06-1.061L10.939 12l-6.47-6.47a.75.75 0 0 1-.072-.976l.073-.084-.073.084Z"
                            fill="#212121" />
                    </svg>
                </button>
            </div>

            <textarea rows="10" class="resize-none" v-model="editPost.message">
            </textarea>

            <p>visibility:
                <span>
                    <input type="radio" name="vis-pub" value="public" v-model="editPost.visibility"> <label
                        for="vis-pub">public </label>
                    <input type="radio" name="vis-pub" value="friends" v-model="editPost.visibility"> <label
                        for="vis-pub">friends </label>
                    <input type="radio" name="vis-me" value="only me" v-model="editPost.visibility"> <label
                        for="vis-me">only me </label>
                </span>
            </p>

            <button type="submit" class="px-4 py-3 border bg-black text-white disabled:bg-gray-500"
                :disabled="editPost.message == post.post && editPost.visibility == post.visibility">
                Edit your post!
            </button>
    </form>
</dialog></template>