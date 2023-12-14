<script setup>
import Comments from './Comments.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watchEffect } from 'vue';
import axios from 'axios';

const props = defineProps(['post']);

const commentToggle = ref(false);
const box = ref(null);
const commentStatus = ref('loading');
const commentsList = ref({});
const comment = useForm({
    'comment': ''
});

const submitComment = function () {
    comment.post('/post/' + props.post.id + '/comments/create', {
        preserveScroll: true,
        onSuccess: () => {
            //TODO: Animate box then toggle
            comment.reset();
        }
    })
}

const toggleComment = function () {
    commentToggle.value = !commentToggle.value;
}

watchEffect(() => {
    if (box.value !== null) {
        const inputbox = box.value.children[1].children[1];
        inputbox.focus();
    }
});

watchEffect(function(){
    if(commentToggle.value == true){
        commentStatus.value = 'loading';
        axios.get('/post/' + props.post.id + '/comments').then(
            function (res) {
                commentStatus.value = res.status;
                commentsList.value = res.data.comments;
                console.log(res);
            } 
        );
    }
});
</script>

<template>
    <div class="flex flex-col justify-end flex-shrink-0">
        <div class="border p-8 min-h-max w-[40rem] flex flex-col gap-y-4 justify-between">
            <!-- PostEntry Header -->
            <div class="flex flex-row justify-between items-center">
                <div class="flex flex-row gap-x-4">
                    <div class="h-12 w-12 bg-gray-300 rounded-full"></div>
                    <div class="flex flex-col">
                        <p>{{ post.user.name }} <span
                                v-if="post.user.username == $page.props.auth.user.username">(You)</span></p>
                        <div class="text-sm">
                            <Link as="a" class="underline underline-offset-1" :href="'/profile/' + post.user.username"
                                v-if="post.user.username != $page.props.auth.user.username">
                            @{{ post.user.username }}
                            </Link>
                            <p v-else>@{{ post.user.username }}</p>
                        </div>
                    </div>
                </div>

                <p>
                    <span v-if="post.user.is_friend == 'no'">
                        <Link as="button" type="button" :href="'/friend/request/add/' + post.user.username" method="post"
                            preserve-scroll preserve-state>+
                        Add as Friend!</Link>
                    </span>
                    <span v-else-if="post.user.is_friend == 'pending'">Request Pending</span>
                    <span v-else-if="post.user.is_friend == 'requested'">
                        <Link as="button" type="button" :href="'/friend/request/accept/' + post.user.username" method="post"
                            preserve-scroll preserve-state>Confirm Request</Link>
                    </span>
                </p>
            </div>

            <!-- PostEntry Body -->
            <p :class="{ 'text-[2.4vw]': post.post.length < 48, 'text-[1.3vw]': post.post.length >= 48 && post.post.length <= 128 }"
                class="break-words flex-grow">{{ post.post }}</p>

            <!-- PostEntry Footer -->
            <div class="flex flex-row gap-x-4 items-center">
                <Link as="button" type="button" method="post" :href="'/post/' + post.id + '/unlike'" class="h-fit"
                    preserve-scroll preserve-state v-if="post.is_liked == true">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="m12.82 5.58-.82.822-.824-.824a5.375 5.375 0 1 0-7.601 7.602l7.895 7.895a.75.75 0 0 0 1.06 0l7.902-7.897a5.376 5.376 0 0 0-.001-7.599 5.38 5.38 0 0 0-7.611 0Z"
                        fill="#212121" />
                </svg>
                </Link>

                <Link as="button" type="button" method="post" :href="'/post/' + post.id + '/like'" class="h-fit"
                    preserve-scroll preserve-state v-else>
                <svg width="24" height="24" class="stroke-black stroke-0" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="m12.82 5.58-.82.822-.824-.824a5.375 5.375 0 1 0-7.601 7.602l7.895 7.895a.75.75 0 0 0 1.06 0l7.902-7.897a5.376 5.376 0 0 0-.001-7.599 5.38 5.38 0 0 0-7.611 0Zm6.548 6.54L12 19.485 4.635 12.12a3.875 3.875 0 1 1 5.48-5.48l1.358 1.357a.75.75 0 0 0 1.073-.012L13.88 6.64a3.88 3.88 0 0 1 5.487 5.48Z" />
                </svg>
                </Link>

                <button type="button" class="h-min" @click="toggleComment">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12.022 3a6.473 6.473 0 0 0-.709 1.5H5.25A1.75 1.75 0 0 0 3.5 6.25v8.5c0 .966.784 1.75 1.75 1.75h2.249v3.75l5.015-3.75h6.236a1.75 1.75 0 0 0 1.75-1.75l.001-2.483a6.517 6.517 0 0 0 1.5-1.077L22 14.75A3.25 3.25 0 0 1 18.75 18h-5.738L8 21.75a1.25 1.25 0 0 1-1.999-1V18h-.75A3.25 3.25 0 0 1 2 14.75v-8.5A3.25 3.25 0 0 1 5.25 3h6.772ZM17.5 1a5.5 5.5 0 1 1 0 11 5.5 5.5 0 0 1 0-11Zm0 1.5-.09.008a.5.5 0 0 0-.402.402L17 3l-.001 3H14l-.09.008a.5.5 0 0 0-.402.402l-.008.09.008.09a.5.5 0 0 0 .402.402L14 7h2.999L17 10l.008.09a.5.5 0 0 0 .402.402l.09.008.09-.008a.5.5 0 0 0 .402-.402L18 10l-.001-3H21l.09-.008a.5.5 0 0 0 .402-.402l.008-.09-.008-.09a.5.5 0 0 0-.402-.402L21 6h-3.001L18 3l-.008-.09a.5.5 0 0 0-.402-.402L17.5 2.5Z"
                            fill="#212121" />
                    </svg>
                </button>

                <Link type="button" as="button" :href="'/post/' + post.id + '/delete'" method="delete" v-if="post.user.id == $page.props.auth.user.id">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 1.75a3.25 3.25 0 0 1 3.245 3.066L15.25 5h5.25a.75.75 0 0 1 .102 1.493L20.5 6.5h-.796l-1.28 13.02a2.75 2.75 0 0 1-2.561 2.474l-.176.006H8.313a2.75 2.75 0 0 1-2.714-2.307l-.023-.174L4.295 6.5H3.5a.75.75 0 0 1-.743-.648L2.75 5.75a.75.75 0 0 1 .648-.743L3.5 5h5.25A3.25 3.25 0 0 1 12 1.75Zm6.197 4.75H5.802l1.267 12.872a1.25 1.25 0 0 0 1.117 1.122l.127.006h7.374c.6 0 1.109-.425 1.225-1.002l.02-.126L18.196 6.5ZM13.75 9.25a.75.75 0 0 1 .743.648L14.5 10v7a.75.75 0 0 1-1.493.102L13 17v-7a.75.75 0 0 1 .75-.75Zm-3.5 0a.75.75 0 0 1 .743.648L11 10v7a.75.75 0 0 1-1.493.102L9.5 17v-7a.75.75 0 0 1 .75-.75Zm1.75-6a1.75 1.75 0 0 0-1.744 1.606L10.25 5h3.5A1.75 1.75 0 0 0 12 3.25Z" fill="#212121"/></svg>
                </Link>

                <p class="text-sm text-black/50">{{ post.like_count }} Like(s)</p>
                <p class="text-sm text-black/50">{{ post.comment_count }} Comment(s)</p>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="border-x border-b max-h-96 w-[40rem] flex flex-col overflow-y-scroll" v-if="commentToggle" ref="box">
            <div class="flex flex-col gap-y-4 p-8">
                <div class="" v-if="commentStatus == 'loading'">The comments are loading...</div>
                <Comments v-for="comment in commentsList.data" :postid="post.id" :comment="comment" v-if="commentStatus == 200 && commentsList.data.length != 0"/>
                <div class="" v-if="commentStatus == '200' && commentsList.data.length == 0">There are no comments yet...</div>
            </div>

            <form class="flex p-6 flex-row gap-x-4 left-8 sticky bottom-0 z-20 bg-white border-t"
                @submit.prevent="submitComment" ref="box">
                <div class="h-10 w-10 bg-gray-300 flex-shrink-0">
                    
                </div>
                <input type="text" name="comment" id="comment" class="h-10 flex-grow placeholder:px-2"
                    placeholder="Comment on this post.." v-model="comment.comment">
                <button type="submit" class="border text-white bg-black px-4 h-10 text-sm w-fit">Comment!</button>
            </form>
        </div>
    </div>
</template>