<script setup>
import { useForm } from '@inertiajs/vue3';

const login = useForm({
    username: '',
    password: '',
});

const create = useForm({
    username: '',
    password: '',
});

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
            <input type="password" name="password" id="password" v-model="login.password" placeholder="Password">
            <button type="submit">Log in!</button>
        </form>

        <form @submit.prevent="createAccount" class="flex flex-col gap-y-6 p-6 border border-black w-96">
            <h1 class="font-medium text-lg">create an account!</h1>
            <input type="text" name="username" id="username" v-model="create.username" placeholder="Username">
            <input type="password" name="password" id="password" v-model="create.password" placeholder="Password">
            <button type="submit">Sign up!</button>
        </form>
    </div>
</template>