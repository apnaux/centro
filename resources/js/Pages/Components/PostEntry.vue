<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps(['post']);

const commentToggle = ref(false);
const comment = useForm({
    'comment': ''
});

const toggleComment = function (){
    commentToggle.value = !commentToggle.value;
}

const submitComment = function (){
    comment.post('/post/' + props.post.id + '/comments/create', {
        onSuccess: () => {
            //TODO: Animate box then toggle
        }
    })
}
</script>

<template>
    <div class="min-h-64 h-80 flex flex-row justify-end gap-x-1 flex-shrink-0">
        <div class="border p-8 min-h-max w-[40rem] flex flex-col gap-y-4 justify-between">
            <div class="flex flex-row justify-between items-center">
                <div class="flex flex-row gap-x-4">
                    <div class="h-12 w-12 bg-gray-300 rounded-full"></div>
                    <div class="flex flex-col">
                        <p>{{ post.user.name }} <span
                                v-if="post.user.username == $page.props.auth.user.username">(You)</span></p>
                        <div class="text-sm">
                            <Link as="a" :href="'/profile/' + post.user.username"
                                v-if="post.user.username != $page.props.auth.user.username">@{{ post.user.username
                                }} </Link>
                            <p v-else>@{{ post.user.username }}</p>
                        </div>
                    </div>
                </div>

                <p>
                    <span v-if="post.user.is_friend == 'no'">
                        <Link as="button" type="button" :href="'/friend/request/add/' + post.user.username" method="post">+
                        Add as Friend!</Link>
                    </span>
                    <span v-else-if="post.user.is_friend == 'pending'">Request Pending</span>
                    <span v-else-if="post.user.is_friend == 'requested'">
                        <Link as="button" type="button" :href="'/friend/request/accept/' + post.user.username"
                            method="post">Confirm Request</Link>
                    </span>
                </p>
            </div>

            <p :class="{ 'text-[2.4vw]': post.post.length < 48, 'text-[1.3vw]': post.post.length >= 48 && post.post.length <= 128 }"
                class="break-words flex-grow">{{ post.post }}</p>

            <div class="flex flex-row gap-x-4 items-center">
                <Link as="button" type="button" :href="'/post/' + post.id + '/like'" class="h-fit">
                <svg width="24" height="24" class="stroke-black stroke-0" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="m12.82 5.58-.82.822-.824-.824a5.375 5.375 0 1 0-7.601 7.602l7.895 7.895a.75.75 0 0 0 1.06 0l7.902-7.897a5.376 5.376 0 0 0-.001-7.599 5.38 5.38 0 0 0-7.611 0Zm6.548 6.54L12 19.485 4.635 12.12a3.875 3.875 0 1 1 5.48-5.48l1.358 1.357a.75.75 0 0 0 1.073-.012L13.88 6.64a3.88 3.88 0 0 1 5.487 5.48Z" />
                </svg>
                </Link>

                <div class="h-6 relative">
                    <button type="button" class="h-min" @click="toggleComment">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12.022 3a6.473 6.473 0 0 0-.709 1.5H5.25A1.75 1.75 0 0 0 3.5 6.25v8.5c0 .966.784 1.75 1.75 1.75h2.249v3.75l5.015-3.75h6.236a1.75 1.75 0 0 0 1.75-1.75l.001-2.483a6.517 6.517 0 0 0 1.5-1.077L22 14.75A3.25 3.25 0 0 1 18.75 18h-5.738L8 21.75a1.25 1.25 0 0 1-1.999-1V18h-.75A3.25 3.25 0 0 1 2 14.75v-8.5A3.25 3.25 0 0 1 5.25 3h6.772ZM17.5 1a5.5 5.5 0 1 1 0 11 5.5 5.5 0 0 1 0-11Zm0 1.5-.09.008a.5.5 0 0 0-.402.402L17 3l-.001 3H14l-.09.008a.5.5 0 0 0-.402.402l-.008.09.008.09a.5.5 0 0 0 .402.402L14 7h2.999L17 10l.008.09a.5.5 0 0 0 .402.402l.09.008.09-.008a.5.5 0 0 0 .402-.402L18 10l-.001-3H21l.09-.008a.5.5 0 0 0 .402-.402l.008-.09-.008-.09a.5.5 0 0 0-.402-.402L21 6h-3.001L18 3l-.008-.09a.5.5 0 0 0-.402-.402L17.5 2.5Z"
                                fill="#212121" />
                        </svg>
                    </button>

                    <form class="flex p-6 flex-col gap-y-3 absolute left-8 top-0 z-20 w-96 bg-white border" @submit.prevent="submitComment" v-if="commentToggle">
                        <h1>Comment on this post!</h1>
                        <textarea name="comment" id="comment" cols="1" rows="5" class="resize-none" v-model="comment.comment"></textarea>
                        <button type="submit" class="border text-white bg-black px-4 py-3 text-sm w-fit">Comment!</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="border min-h-full w-[28rem] flex flex-col gap-y-4 overflow-y-scroll">
            <p class="font-light flex flex-row items-center gap-x-2 sticky top-0 px-8 pt-6 pb-2 bg-white">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" height="24" width="24">
                        <path xmlns="http://www.w3.org/2000/svg"
                            d="M2 6C2 4.89543 2.89543 4 4 4H20C21.1046 4 22 4.89543 22 6V17C22 18.1046 21.1046 19 20 19H15.4142L12.7071 21.7071C12.3166 22.0976 11.6834 22.0976 11.2929 21.7071L8.58579 19H4C2.89543 19 2 18.1046 2 17V6ZM20 6H4V17H9C9.26522 17 9.51957 17.1054 9.70711 17.2929L12 19.5858L14.2929 17.2929C14.4804 17.1054 14.7348 17 15 17H20V6ZM6 9.5C6 8.94772 6.44772 8.5 7 8.5H17C17.5523 8.5 18 8.94772 18 9.5C18 10.0523 17.5523 10.5 17 10.5H7C6.44772 10.5 6 10.0523 6 9.5ZM6 13.5C6 12.9477 6.44772 12.5 7 12.5H13C13.5523 12.5 14 12.9477 14 13.5C14 14.0523 13.5523 14.5 13 14.5H7C6.44772 14.5 6 14.0523 6 13.5Z"
                            fill="#0D0D0D"></path>
                    </svg>
                </span>

                Post Comments
            </p>

            <div class="flex flex-col gap-y-4 px-8 pb-8">
                <div class="flex flex-row items-start justify-between gap-x-4" v-for="n in 10">
                    <div class="h-12 w-12 bg-gray-300 flex-shrink-0">

                    </div>

                    <div class="flex flex-col text-sm">
                        <p class="font-medium">@theuser123</p>
                        <p>dolor sit amet consectetur adipiscing elit pellentesque habitant morbi tristique senectus
                            et netus et malesuada</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>