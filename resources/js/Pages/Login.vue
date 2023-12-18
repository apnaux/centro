<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const login = useForm({
    username: '',
    password: '',
});

const create = useForm({
    username: '',
    password: '',
});

const passwordCreateType = ref('password');

function loginAccount() {
    login.post('/login', {});
}

function createAccount() {
    create.post('/register', {});
}
</script>

<template>
    <div class="w-screen h-screen flex flex-col justify-center items-center gap-y-4">
        <form @submit.prevent="loginAccount" class="flex flex-col gap-y-6 p-6 border border-black w-96">
            <h1 class="font-medium text-lg">log in with your account!</h1>
            <input type="text" name="username" id="username" v-model="login.username" placeholder="Username">
            <p v-if="login.errors.username" class="text-sm">{{ login.errors.username }}</p>
            <input type="password" name="password" id="password" v-model="login.password" placeholder="Password">
            <p v-if="login.errors.password" class="text-sm">{{ login.errors.password }}</p>
            <button type="submit">Log in!</button>
        </form>

        <form @submit.prevent="createAccount" class="flex flex-col gap-y-6 p-6 border border-black w-96">
            <h1 class="font-medium text-lg">create an account!</h1>
            <input type="text" name="username" id="username" v-model="create.username" placeholder="Username">
            <p v-if="create.errors.username" class="text-sm">{{ login.errors.username }}</p>
            <div class="flex flex-row">
                <input class="flex-grow" :type="passwordCreateType" name="password" id="password" v-model="create.password"
                    placeholder="Password">
                <button class="flex-shrink w-fit" type="button" @mousedown="() => { passwordCreateType = 'text' }"
                    @mouseup="() => { passwordCreateType = 'password' }">Show</button>
            </div>
            <p v-if="create.errors.password" class="text-sm">{{ create.errors.password }}</p>
            <button type="submit">Sign up!</button>
        </form>
    </div>
</template>