<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps(['postid', 'comment']);
const emit = defineEmits(['deletion', 'liked']);

const likedComment = ref(props.comment.is_liked);

function delete_comment(){
    router.delete('/post/' + props.postid + '/comments/' + props.comment.id + '/delete', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: function () {
            emit('deletion');
        }
    })
}

function like_comment(){
    likedComment.value = true;
    router.post('/post/' + props.postid + '/comments/' + props.comment.id + '/like', {}, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: function(){
            emit('liked');
        }
    })
}

function unlike_comment(){
    likedComment.value = false;
    router.delete('/post/' + props.postid + '/comments/' + props.comment.id + '/unlike', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: function(){
            emit('liked');
        }
    })
}


</script>

<template>
    <div class="flex flex-row items-start justify-start gap-x-4">
        <div class="h-12 w-12 bg-gray-300 flex-shrink-0">

        </div>

        <div class="flex flex-col text-sm gap-y-1">
            <p class="font-medium">{{ comment.user.name }} @{{ comment.user.username }}</p>
            <p>{{ comment.comment }}</p>

            <div class="flex flex-row gap-x-2">
                <button type="button" v-if="likedComment" @click="unlike_comment">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="m12.82 5.58-.82.822-.824-.824a5.375 5.375 0 1 0-7.601 7.602l7.895 7.895a.75.75 0 0 0 1.06 0l7.902-7.897a5.376 5.376 0 0 0-.001-7.599 5.38 5.38 0 0 0-7.611 0Z"
                        fill="#212121" />
                </svg>
                </button>
                <button type="button" v-else @click="like_comment">
                <svg width="24" height="24" class="stroke-black stroke-0" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="m12.82 5.58-.82.822-.824-.824a5.375 5.375 0 1 0-7.601 7.602l7.895 7.895a.75.75 0 0 0 1.06 0l7.902-7.897a5.376 5.376 0 0 0-.001-7.599 5.38 5.38 0 0 0-7.611 0Zm6.548 6.54L12 19.485 4.635 12.12a3.875 3.875 0 1 1 5.48-5.48l1.358 1.357a.75.75 0 0 0 1.073-.012L13.88 6.64a3.88 3.88 0 0 1 5.487 5.48Z" />
                </svg>
                </button>

                <button type="button" @click="delete_comment"
                    v-if="comment.user.id == $page.props.auth.user.id">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12 1.75a3.25 3.25 0 0 1 3.245 3.066L15.25 5h5.25a.75.75 0 0 1 .102 1.493L20.5 6.5h-.796l-1.28 13.02a2.75 2.75 0 0 1-2.561 2.474l-.176.006H8.313a2.75 2.75 0 0 1-2.714-2.307l-.023-.174L4.295 6.5H3.5a.75.75 0 0 1-.743-.648L2.75 5.75a.75.75 0 0 1 .648-.743L3.5 5h5.25A3.25 3.25 0 0 1 12 1.75Zm6.197 4.75H5.802l1.267 12.872a1.25 1.25 0 0 0 1.117 1.122l.127.006h7.374c.6 0 1.109-.425 1.225-1.002l.02-.126L18.196 6.5ZM13.75 9.25a.75.75 0 0 1 .743.648L14.5 10v7a.75.75 0 0 1-1.493.102L13 17v-7a.75.75 0 0 1 .75-.75Zm-3.5 0a.75.75 0 0 1 .743.648L11 10v7a.75.75 0 0 1-1.493.102L9.5 17v-7a.75.75 0 0 1 .75-.75Zm1.75-6a1.75 1.75 0 0 0-1.744 1.606L10.25 5h3.5A1.75 1.75 0 0 0 12 3.25Z"
                        fill="#212121" />
                </svg>
                </button>
            </div>
        </div>
    </div>
</template>