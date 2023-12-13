<script setup>
import PostEntry from './Components/PostEntry.vue';
import { useForm } from '@inertiajs/vue3';

const message = useForm({
    message: '',
    visibility: 'friends',
});

function postMessage() {
    message.post("/post/create", {
        onSuccess: () => {
            message.reset();
        }
    });
}
</script>

<template>
    <div class="flex flex-row justify-between overflow-visible">
        <div class="h-screen flex flex-col gap-y-4 sticky top-0 py-20 ml-48 min-w-[20rem]">
            <div class="border h-20"></div>
            <div class="border h-12 p-2 flex flex-row justify-evenly">
                <Link as="button" type="button" href="/home" method="get">friends</Link>
                <Link as="button" type="button" href="/home/public" method="get">public</Link>
                <Link as="button" type="button" href="/notifications" method="get">notifs.</Link>
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
            <div class="border h-48 flex flex-col mt-12">
                <div class="flex flex-col items-center gap-y-4 relative -top-1/4">
                    <div class="h-24 w-24 bg-gray-300">

                    </div>

                    <div class="flex flex-col leading-none items-center">
                        <p class="text-lg font-medium">{{ $page.props.auth.user.name }}</p>
                        <p>@{{ $page.props.auth.user.username }}</p>
                    </div>
                </div>
                <Link as="button" type="button" href="/logout" method="post">logout!</Link>
            </div>
        </div>

        <div class="flex flex-col gap-y-6 flex-shrink-0 py-20 pr-48">
            <PostEntry :post="post" v-for="post in $page.props.paginated.data"/>
        </div>
    </div>
</template>