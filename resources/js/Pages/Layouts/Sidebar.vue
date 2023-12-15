<script setup>
import { useForm } from '@inertiajs/vue3';
import { onBeforeMount, onMounted, ref, watch } from 'vue';

const props = defineProps(['auth']);
const toggleRequest = ref(false);
const requestsCount = ref(0);
const friendRequests = ref({});

const message = useForm({
    message: '',
    visibility: 'friends',
});

function postMessage() {
    message.post("/post/create", {
        preserveState: false,
        onSuccess: () => {
            message.reset();
        }
    });
}

onBeforeMount(function () {
    axios.get('/friend/request/count').then(
        function (res) {
            requestsCount.value = res.data.friendRequestCount;
        }
    );
});

onMounted(function () {
    setInterval(function () {
        axios.get('/friend/request/count').then(
            function (res) {
                requestsCount.value = res.data.friendRequestCount;
                console.log(res);
            }
        );
    }, 30000);
});

watch(toggleRequest, (x) => {
    if (x) {
        axios.get('/friend/request').then(
            function (res) {
                friendRequests.value = res.data.friendRequests;
            }
        );
    }
});
</script>

<template>
    <div class="flex flex-row justify-between overflow-visible">
        <div class="flex flex-col gap-y-4 sticky top-0 py-20 ml-48 min-w-[22rem] max-h-screen">
            <div class="border flex flex-row justify-between items-center">
                <div class="flex flex-row items-center gap-x-4">
                    <div class="h-20 w-20 bg-gray-300">

                    </div>

                    <div class="flex flex-col">
                        <p class="font-medium leading-none">{{ $page.props.auth.user.name }}</p>
                        <p class="text-sm">@{{ $page.props.auth.user.username }}</p>
                    </div>
                </div>

                <div class="flex flex-row gap-x-2 items-center mr-4">
                    <Link as="button" type="button" href="">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M17.754 14a2.249 2.249 0 0 1 2.25 2.249v.575c0 .894-.32 1.76-.902 2.438-1.57 1.834-3.957 2.739-7.102 2.739-3.146 0-5.532-.905-7.098-2.74a3.75 3.75 0 0 1-.898-2.435v-.577a2.249 2.249 0 0 1 2.249-2.25h11.501Zm0 1.5H6.253a.749.749 0 0 0-.75.749v.577c0 .536.192 1.054.54 1.461 1.253 1.468 3.219 2.214 5.957 2.214s4.706-.746 5.962-2.214a2.25 2.25 0 0 0 .541-1.463v-.575a.749.749 0 0 0-.749-.75ZM12 2.004a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Z"
                            fill="#212121" />
                    </svg>
                    </Link>

                    <Link as="button" type="button" href="/logout" method="post">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.502 11.5a1.002 1.002 0 1 1 0 2.004 1.002 1.002 0 0 1 0-2.004Z" fill="#212121" />
                        <path
                            d="M12 4.354v6.651l7.442-.001L17.72 9.28a.75.75 0 0 1-.073-.976l.073-.084a.75.75 0 0 1 .976-.073l.084.073 2.997 2.997a.75.75 0 0 1 .073.976l-.073.084-2.996 3.004a.75.75 0 0 1-1.134-.975l.072-.085 1.713-1.717-7.431.001L12 19.25a.75.75 0 0 1-.88.739l-8.5-1.502A.75.75 0 0 1 2 17.75V5.75a.75.75 0 0 1 .628-.74l8.5-1.396a.75.75 0 0 1 .872.74Zm-1.5.883-7 1.15V17.12l7 1.236V5.237Z"
                            fill="#212121" />
                        <path
                            d="M13 18.501h.765l.102-.006a.75.75 0 0 0 .648-.745l-.007-4.25H13v5.001ZM13.002 10 13 8.725V5h.745a.75.75 0 0 1 .743.647l.007.102.007 4.251h-1.5Z"
                            fill="#212121" />
                    </svg>
                    </Link>
                </div>
            </div>
            <form @submit.prevent="postMessage" class="border h-72 p-4 flex flex-col justify-between">
                <h1 class="font-medium text-lg">write a post!</h1>
                <textarea name="post" id="post" cols="1" rows="5" v-model="message.message" class="resize-none"></textarea>
                <p>visibility: <span>
                        <input type="radio" name="vis-pub" value="public" v-model="message.visibility"> <label
                            for="vis-pub">public </label>
                        <input type="radio" name="vis-pub" value="friends" v-model="message.visibility"> <label
                            for="vis-pub">friends </label>
                        <input type="radio" name="vis-me" value="only me" v-model="message.visibility"> <label
                            for="vis-me">only me </label>
                    </span></p>
                <button type="submit" class="w-fit">cend!</button>
            </form>
        </div>

        <div class="flex flex-col gap-y-6 py-20 flex-shrink-0">
            <slot></slot>
        </div>

        <div class="flex flex-col gap-y-4 sticky top-0 left-0 py-20 mr-48 min-w-[22rem] max-h-screen">
            <div class="h-12 p-2 flex flex-row justify-evenly">
                <Link as="button" type="button" href="/home" method="get">friends</Link>
                <Link as="button" type="button" href="/home/public" method="get">public</Link>
                <Link as="button" type="button" href="/home/notifications" method="get">notifs.</Link>
            </div>
            <div>
                <button type="button" class="border py-6 px-6 flex flex-row justify-between w-full"
                    @click="() => { toggleRequest = !toggleRequest }">
                    Friend Requests ({{ requestsCount }})
                    <p>></p>
                </button>

                <div class="flex flex-col max-h-64 border border-t-0" v-if="toggleRequest">
                    <div class="flex flex-col px-4 py-3 gap-y-2 border-b" v-for="request in friendRequests">
                        <div class="flex flex-row gap-x-4 items-center">
                            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M17.5 12a5.5 5.5 0 1 1 0 11 5.5 5.5 0 0 1 0-11Zm-5.477 2a6.47 6.47 0 0 0-.709 1.5H4.253a.749.749 0 0 0-.75.75v.577c0 .535.192 1.053.54 1.46 1.253 1.469 3.22 2.214 5.957 2.214.597 0 1.157-.035 1.68-.106.246.495.553.954.912 1.367-.795.16-1.66.24-2.592.24-3.146 0-5.532-.906-7.098-2.74a3.75 3.75 0 0 1-.898-2.435v-.578A2.249 2.249 0 0 1 4.253 14h7.77Zm5.477 0-.09.008a.5.5 0 0 0-.402.402L17 14.5V17h-2.496l-.09.008a.5.5 0 0 0-.402.402l-.008.09.008.09a.5.5 0 0 0 .402.402l.09.008H17L17 20.5l.008.09a.5.5 0 0 0 .402.402l.09.008.09-.008a.5.5 0 0 0 .402-.402L18 20.5V18h2.504l.09-.008a.5.5 0 0 0 .402-.402l.008-.09-.008-.09a.5.5 0 0 0-.402-.402l-.09-.008H18L18 14.5l-.008-.09a.5.5 0 0 0-.402-.402L17.5 14ZM10 2.005a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Z"
                                    fill="#212121" />
                            </svg>
                            <p class="leading-5">
                                <span class="font-medium">@{{ request.user.username }}</span>
                                <br>
                                wants you to be their friend!
                            </p>

                        </div>

                        <div class="flex flex-row gap-x-2 ml-10">
                            <button type="button">Accept</button>
                            <button type="button">Decline</button>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <button type="button" class="border py-6 px-6 flex flex-row justify-between w-full"
                    @click="() => { toggleRequest = !toggleRequest }">
                    Your Notifications ({{ requestsCount }})
                    <p>></p>
                </button>

                <div class="flex flex-col max-h-64 border border-t-0" v-if="toggleRequest">
                    <div class="flex flex-col px-4 py-3 gap-y-2 border-b" v-for="request in friendRequests">
                        <div class="flex flex-row gap-x-4 items-center">
                            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M17.5 12a5.5 5.5 0 1 1 0 11 5.5 5.5 0 0 1 0-11Zm-5.477 2a6.47 6.47 0 0 0-.709 1.5H4.253a.749.749 0 0 0-.75.75v.577c0 .535.192 1.053.54 1.46 1.253 1.469 3.22 2.214 5.957 2.214.597 0 1.157-.035 1.68-.106.246.495.553.954.912 1.367-.795.16-1.66.24-2.592.24-3.146 0-5.532-.906-7.098-2.74a3.75 3.75 0 0 1-.898-2.435v-.578A2.249 2.249 0 0 1 4.253 14h7.77Zm5.477 0-.09.008a.5.5 0 0 0-.402.402L17 14.5V17h-2.496l-.09.008a.5.5 0 0 0-.402.402l-.008.09.008.09a.5.5 0 0 0 .402.402l.09.008H17L17 20.5l.008.09a.5.5 0 0 0 .402.402l.09.008.09-.008a.5.5 0 0 0 .402-.402L18 20.5V18h2.504l.09-.008a.5.5 0 0 0 .402-.402l.008-.09-.008-.09a.5.5 0 0 0-.402-.402l-.09-.008H18L18 14.5l-.008-.09a.5.5 0 0 0-.402-.402L17.5 14ZM10 2.005a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Z"
                                    fill="#212121" />
                            </svg>
                            <p class="leading-5">
                                <span class="font-medium">@{{ request.user.username }}</span>
                                <br>
                                wants you to be their friend!
                            </p>

                        </div>

                        <div class="flex flex-row gap-x-2 ml-10">
                            <button type="button">Accept</button>
                            <button type="button">Decline</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>