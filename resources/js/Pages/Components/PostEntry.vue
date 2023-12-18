<script setup>
import EditPost from './EditPost.vue';
import Comments from './Comments.vue';
import { getDateString, calculateIfEdited } from '../../functions/dates.js';
import { router, useForm } from '@inertiajs/vue3';
import { ref, watch, watchEffect } from 'vue';
import axios from 'axios';

const props = defineProps(['post', 'index']);
const emits = defineEmits(['deleted']);

const box = ref(null);
const edit = ref(null);

const likedPost = ref(props.post.is_liked);
const commentCount = ref(props.post.comment_count);
const likeCount = ref(props.post.like_count);

const commentToggle = ref(false);
const commentStatus = ref('loading');
const commentsList = ref({});
const comment = useForm({
    'comment': ''
});

const post_info = ref(props.post);

const submitComment = function () {
    comment.post('/post/' + props.post.id + '/comments/create', {
        preserveScroll: true,
        onSuccess: () => {
            //TODO: Animate box then toggle
            retrieve_comments();
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

watchEffect(function () {
    if (commentToggle.value == true) {
        commentStatus.value = 'loading';
        retrieve_comments();
        friend_request_check()
    }
});

function likePost(){
    if(likedPost.value) {
        router.delete('/post/' + props.post.id + '/unlike', {
            preserveScroll: true,
            preserveState: true,
            onStart: function () {
                likedPost.value = false;
            },
            onSuccess: function (){
                retrieve_is_liked();
                get_like_count();
                friend_request_check()
            }
        })
    } else if (likedPost.value == false)  {
        router.post('/post/' + props.post.id + '/like', {}, {
            preserveScroll: true,
            preserveState: true,
            onSubmit: function(){
                likedPost.value = true;
            },
            onSuccess: function (){
                retrieve_is_liked();
                get_like_count();
                friend_request_check()
            }
        })
    }
}

function retrieve_is_liked(){
    axios.get('/post/' + props.post.id + '/like/check').then(
        function (res) {
            console.log(res.data);
            likedPost.value = res.data;
        }
    ).finally(() => {
        friend_request_check()
    });
}

function retrieve_comments() {
    console.log(post_info.value);
    axios.get('/post/' + props.post.id + '/comments').then(
        function (res) {
            commentStatus.value = res.status;
            commentsList.value = res.data.comments;
            console.log(res);
        }
    ).finally(
        function (){
            get_comment_count();
            friend_request_check()
        }
    );
}

function get_comment_count(){
    axios.get('/post/' + props.post.id + '/comments/count').then(
        function (res){
            commentCount.value = res.data;
        }
    ).finally(() => {
        friend_request_check()
    });
}

function get_like_count(){
    axios.get('/post/' + props.post.id + '/like/count').then(
        function (res){
            likeCount.value = res.data
        }
    ).finally(() => {
        friend_request_check()
    });
}

function update_post(){
    console.log(post_info.value);
    axios.get('/post/' + props.post.id).then(
        function (res) {
            post_info.value.post = res.data.post.post;
            post_info.value.visibility = res.data.post.visibility;
            post_info.value.updated_at = res.data.post.updated_at;
        }
    ).finally(() => {
        friend_request_check()
    });
}

function friend_request_check(){
    axios.get('/post/' + props.post.id + '/friend/check').then(
        function (res) {
            post_info.value.user.is_friend = res.data.is_friend;
            post_info.value.user.friend_request = res.data.friend_request;
        }
    );
}
</script>

<template>
    <EditPost ref="edit" :post="post_info" @updated="update_post"/>

    <div class="flex flex-col justify-end flex-shrink-0">
        <div class="border p-8 min-h-max w-[40rem] flex flex-col gap-y-4 justify-between">
            <!-- PostEntry Header -->
            <div class="flex flex-row justify-between items-center">
                <div class="flex flex-row gap-x-4">
                    <div class="h-12 w-12 bg-gray-300 rounded-full"></div>
                    <div class="flex flex-col">
                        <div>
                            <Link as="a" class="underline underline-offset-1" :href="'/profile/' + post_info.user.username"
                                v-if="post_info.user.username != $page.props.auth.user.username">
                            {{ post_info.user.name }} @{{ post_info.user.username }}
                            </Link>
                            <p v-else>{{ post_info.user.name }} @{{ post_info.user.username }} (You)</p>
                        </div>
                        <div class="text-gray-400 text-sm flex flex-row gap-x-2 items-center">
                            <svg width="24" height="24" v-if="post.visibility == 'only me'" class="w-4" fill="none"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M14 14H4.253a2.249 2.249 0 0 0-2.25 2.249v.578c0 .892.32 1.756.9 2.435 1.565 1.834 3.951 2.74 7.097 2.74.715 0 1.39-.048 2.026-.141A2.524 2.524 0 0 1 12 21.5v-1.153c-.614.103-1.28.154-2 .154-2.738 0-4.704-.746-5.957-2.213a2.25 2.25 0 0 1-.54-1.462v-.577c0-.414.336-.75.75-.75h7.955A2.505 2.505 0 0 1 14 14.05V14ZM10 2.005a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7ZM15 15h-.5a1.5 1.5 0 0 0-1.5 1.5v5a1.5 1.5 0 0 0 1.5 1.5h6a1.5 1.5 0 0 0 1.5-1.5v-5a1.5 1.5 0 0 0-1.5-1.5H20v-1a2.5 2.5 0 0 0-5 0v1Zm1.5-1a1 1 0 1 1 2 0v1h-2v-1Zm2 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z"
                                    fill="#212121" />
                            </svg>
                            <svg width="24" height="24" v-if="post.visibility == 'friends'" class="w-4" fill="none"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M4 13.999 13 14a2 2 0 0 1 1.995 1.85L15 16v1.5C14.999 21 11.284 22 8.5 22c-2.722 0-6.335-.956-6.495-4.27L2 17.5v-1.501c0-1.054.816-1.918 1.85-1.995L4 14ZM15.22 14H20c1.054 0 1.918.816 1.994 1.85L22 16v1c-.001 3.062-2.858 4-5 4a7.16 7.16 0 0 1-2.14-.322c.336-.386.607-.827.802-1.327A6.19 6.19 0 0 0 17 19.5l.267-.006c.985-.043 3.086-.363 3.226-2.289L20.5 17v-1a.501.501 0 0 0-.41-.492L20 15.5h-4.051a2.957 2.957 0 0 0-.595-1.34L15.22 14H20h-4.78ZM4 15.499l-.1.01a.51.51 0 0 0-.254.136.506.506 0 0 0-.136.253l-.01.101V17.5c0 1.009.45 1.722 1.417 2.242.826.445 2.003.714 3.266.753l.317.005.317-.005c1.263-.039 2.439-.308 3.266-.753.906-.488 1.359-1.145 1.412-2.057l.005-.186V16a.501.501 0 0 0-.41-.492L13 15.5l-9-.001ZM8.5 3a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9Zm9 2a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7Zm-9-.5c-1.654 0-3 1.346-3 3s1.346 3 3 3 3-1.346 3-3-1.346-3-3-3Zm9 2c-1.103 0-2 .897-2 2s.897 2 2 2 2-.897 2-2-.897-2-2-2Z"
                                    fill="#212121" />
                            </svg>
                            <svg width="24" height="24" v-if="post.visibility == 'public'" class="w-4" fill="none"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="m10.946 2.047.005.007C11.296 2.018 11.646 2 12 2c5.522 0 10 4.477 10 10s-4.478 10-10 10a9.983 9.983 0 0 1-7.896-3.862h-.003v-.003A9.957 9.957 0 0 1 2 12c0-5.162 3.911-9.41 8.932-9.944l.014-.009ZM12 3.5l-.16.001c.123.245.255.533.374.85.347.923.666 2.282.1 3.487-.522 1.113-1.424 1.4-2.09 1.573l-.084.021c-.657.17-.91.235-1.093.514-.17.257-.144.582.061 1.25l.046.148c.082.258.18.57.23.863.064.364.082.827-.152 1.275a2.187 2.187 0 0 1-.9.945c-.341.185-.694.256-.958.302l-.093.017c-.515.09-.761.134-1 .39-.187.2-.307.553-.377 1.079-.029.214-.046.427-.064.646l-.01.117c-.02.242-.044.521-.099.76v.002a8.478 8.478 0 0 0 6.27 2.76c1.576 0 3.053-.43 4.319-1.178a4.47 4.47 0 0 1-.31-.35c-.34-.428-.786-1.164-.631-2.033.074-.418.298-.768.515-1.036a7.12 7.12 0 0 1 .72-.74l.158-.146c.179-.163.33-.301.46-.437.172-.18.21-.262.212-.267.068-.224-.015-.384-.106-.454a.304.304 0 0 0-.19-.061c-.084 0-.22.024-.401.14a.912.912 0 0 1-.836.085 1.025 1.025 0 0 1-.486-.432c-.144-.237-.225-.546-.278-.772-.04-.174-.08-.372-.115-.553l-.04-.206a4.127 4.127 0 0 0-.134-.54l-.02-.037a1.507 1.507 0 0 0-.064-.105 6.233 6.233 0 0 0-.227-.317l-.11-.143a12.686 12.686 0 0 1-.516-.712c-.196-.298-.417-.688-.487-1.104a1.46 1.46 0 0 1 .055-.734c.094-.264.265-.482.487-.649.483-.362 1.193-1.172 1.823-1.959.288-.359.544-.695.736-.95A8.46 8.46 0 0 0 12 3.5Zm5.727 2.22c-.197.263-.461.608-.757.978-.602.751-1.4 1.685-2.05 2.187.026.1.1.262.255.498.131.2.281.396.44.604l.129.17c.172.229.411.548.52.844.087.234.149.519.198.762l.049.246c.025.13.049.253.075.37.601-.172 1.201-.068 1.67.294.608.47.862 1.286.624 2.074-.11.362-.364.66-.563.869-.17.177-.372.362-.556.53l-.132.12c-.23.212-.423.4-.568.579-.148.184-.195.299-.205.356-.04.219.067.51.328.838a3.138 3.138 0 0 0 .374.392A8.48 8.48 0 0 0 20.5 12a8.478 8.478 0 0 0-2.773-6.28ZM3.5 12c0 1.398.338 2.718.936 3.881.085-.557.262-1.248.748-1.768.6-.642 1.335-.763 1.798-.839l.13-.021c.248-.044.391-.083.502-.143.088-.049.188-.128.288-.321.015-.028.042-.107.004-.325a5.236 5.236 0 0 0-.172-.636c-.02-.06-.04-.125-.06-.192-.185-.604-.48-1.602.12-2.515.522-.792 1.36-.994 1.893-1.123l.162-.04c.563-.145.883-.28 1.108-.758.295-.629.168-1.485-.146-2.32a7.615 7.615 0 0 0-.58-1.196A8.503 8.503 0 0 0 3.502 12Z"
                                    fill="#212121" />
                            </svg>
                            <p>
                                {{ getDateString(post_info.created_at) }} <span
                                    v-if="calculateIfEdited(post_info.created_at, post_info.updated_at)">(edited)</span>
                            </p>
                        </div>
                    </div>
                </div>

                <p>
                    <span v-if="post_info.user.is_friend == false && post_info.user.friend_request.pending">Request Pending</span>
                    <span v-else-if="post_info.user.is_friend == false && post_info.user.friend_request.confirm">
                        <Link as="button" type="button" @click="friend_request_check" :href="'/friend/request/accept/' + post_info.user.username" method="post"
                            preserve-scroll preserve-state>Confirm Request</Link>
                    </span>
                    <span v-else-if="post_info.user.is_friend == false">
                        <Link as="button" type="button" @click="friend_request_check"  :href="'/friend/request/add/' + post_info.user.username" method="post"
                            preserve-scroll preserve-state>+
                        Add as Friend!</Link>
                    </span>
                </p>
            </div>

            <!-- PostEntry Body -->
            <p :class="{ 'text-[2.4vw]': post_info.post.length < 48, 'text-[1.3vw]': post_info.post.length >= 48 && post.post.length <= 128 }"
                class="break-words flex-grow">{{ post_info.post }}</p>

            <!-- PostEntry Footer -->
            <div class="flex flex-row gap-x-4 items-center">
                <button type="button" @click="likePost" v-if="likedPost">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="m12.82 5.58-.82.822-.824-.824a5.375 5.375 0 1 0-7.601 7.602l7.895 7.895a.75.75 0 0 0 1.06 0l7.902-7.897a5.376 5.376 0 0 0-.001-7.599 5.38 5.38 0 0 0-7.611 0Z"
                        fill="#212121" />
                </svg>
                </button>

                <button type="button" @click="likePost" v-else>
                <svg width="24" height="24" class="stroke-black stroke-0" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="m12.82 5.58-.82.822-.824-.824a5.375 5.375 0 1 0-7.601 7.602l7.895 7.895a.75.75 0 0 0 1.06 0l7.902-7.897a5.376 5.376 0 0 0-.001-7.599 5.38 5.38 0 0 0-7.611 0Zm6.548 6.54L12 19.485 4.635 12.12a3.875 3.875 0 1 1 5.48-5.48l1.358 1.357a.75.75 0 0 0 1.073-.012L13.88 6.64a3.88 3.88 0 0 1 5.487 5.48Z" />
                </svg>
                </button>

                <button type="button" class="h-min" @click="toggleComment">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12.022 3a6.473 6.473 0 0 0-.709 1.5H5.25A1.75 1.75 0 0 0 3.5 6.25v8.5c0 .966.784 1.75 1.75 1.75h2.249v3.75l5.015-3.75h6.236a1.75 1.75 0 0 0 1.75-1.75l.001-2.483a6.517 6.517 0 0 0 1.5-1.077L22 14.75A3.25 3.25 0 0 1 18.75 18h-5.738L8 21.75a1.25 1.25 0 0 1-1.999-1V18h-.75A3.25 3.25 0 0 1 2 14.75v-8.5A3.25 3.25 0 0 1 5.25 3h6.772ZM17.5 1a5.5 5.5 0 1 1 0 11 5.5 5.5 0 0 1 0-11Zm0 1.5-.09.008a.5.5 0 0 0-.402.402L17 3l-.001 3H14l-.09.008a.5.5 0 0 0-.402.402l-.008.09.008.09a.5.5 0 0 0 .402.402L14 7h2.999L17 10l.008.09a.5.5 0 0 0 .402.402l.09.008.09-.008a.5.5 0 0 0 .402-.402L18 10l-.001-3H21l.09-.008a.5.5 0 0 0 .402-.402l.008-.09-.008-.09a.5.5 0 0 0-.402-.402L21 6h-3.001L18 3l-.008-.09a.5.5 0 0 0-.402-.402L17.5 2.5Z"
                            fill="#212121" />
                    </svg>
                </button>

                <Link @click="$emit('deleted', props.index)" type="button" as="button" :href="'/post/' + post.id + '/delete'" method="delete"
                    v-if="post_info.user.id == $page.props.auth.user.id" preserve-scroll>
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M12 1.75a3.25 3.25 0 0 1 3.245 3.066L15.25 5h5.25a.75.75 0 0 1 .102 1.493L20.5 6.5h-.796l-1.28 13.02a2.75 2.75 0 0 1-2.561 2.474l-.176.006H8.313a2.75 2.75 0 0 1-2.714-2.307l-.023-.174L4.295 6.5H3.5a.75.75 0 0 1-.743-.648L2.75 5.75a.75.75 0 0 1 .648-.743L3.5 5h5.25A3.25 3.25 0 0 1 12 1.75Zm6.197 4.75H5.802l1.267 12.872a1.25 1.25 0 0 0 1.117 1.122l.127.006h7.374c.6 0 1.109-.425 1.225-1.002l.02-.126L18.196 6.5ZM13.75 9.25a.75.75 0 0 1 .743.648L14.5 10v7a.75.75 0 0 1-1.493.102L13 17v-7a.75.75 0 0 1 .75-.75Zm-3.5 0a.75.75 0 0 1 .743.648L11 10v7a.75.75 0 0 1-1.493.102L9.5 17v-7a.75.75 0 0 1 .75-.75Zm1.75-6a1.75 1.75 0 0 0-1.744 1.606L10.25 5h3.5A1.75 1.75 0 0 0 12 3.25Z"
                        fill="#212121" />
                </svg>
                </Link>

                <button type="button" @click="edit.show()" v-if="post_info.user.id == $page.props.auth.user.id">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M6.25 3.5a.75.75 0 0 0-.75.75v15.5c0 .414.336.75.75.75h3.78a2.077 2.077 0 0 0 .27 1.5H6.25A2.25 2.25 0 0 1 4 19.75V4.25A2.25 2.25 0 0 1 6.25 2h6.086c.464 0 .909.184 1.237.513l5.914 5.914c.329.328.513.773.513 1.237V10h-.13a3.324 3.324 0 0 0-.332 0H14a2 2 0 0 1-2-2V3.5H6.25Zm7.25 1.06V8a.5.5 0 0 0 .5.5h3.44L13.5 4.56Z"
                            fill="#212121" />
                        <path
                            d="M19.713 11h.002a2.286 2.286 0 0 1 1.615 3.902l-5.902 5.902a2.684 2.684 0 0 1-1.247.707l-1.831.457a1.087 1.087 0 0 1-1.318-1.318l.457-1.83c.118-.473.362-.904.707-1.248l5.902-5.902a2.278 2.278 0 0 1 1.615-.67Z"
                            fill="#212121" />
                    </svg>
                </button>

                <p class="text-sm text-black/50">{{ likeCount }} Like(s)</p>
                <p class="text-sm text-black/50">{{ commentCount }} Comment(s)</p>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="border-x border-b max-h-96 w-[40rem] flex flex-col overflow-y-scroll" v-if="commentToggle" ref="box">
            <div class="flex flex-col gap-y-4 p-8">
                <div class="" v-if="commentStatus == 'loading'">The comments are loading...</div>
                <Comments @deletion="retrieve_comments" @liked="retrieve_comments" v-for="comment in commentsList.data" :postid="post.id" :comment="comment"
                    v-if="commentStatus == 200 && commentsList.data.length != 0" />
                <div class="" v-if="commentStatus == '200' && commentsList.data.length == 0">There are no comments yet...
                </div>
            </div>

            <form class="flex p-6 flex-row gap-x-4 left-8 sticky bottom-0 z-20 bg-white border-t"
                @submit.prevent="submitComment" ref="box">
                <div class="h-10 w-10 bg-gray-300 flex-shrink-0">

                </div>
                <input type="text" name="comment" id="comment" class="h-10 flex-grow placeholder:px-2"
                    placeholder="Comment on this post.." v-model="comment.comment" autocomplete="off">
                <button type="submit" class="border text-white bg-black px-4 h-10 text-sm w-fit">Comment!</button>
            </form>
        </div>
</div></template>