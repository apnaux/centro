<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const comment = useForm({
    'comment': ''
});

const submitComment = function () {
    comment.post('/post/' + props.post.id + '/comments/create', {
        onSuccess: () => {
            //TODO: Animate box then toggle
        }
    })
}
</script>

<template>
    <div class="border-x border-b max-h-96 w-[40rem] flex flex-col overflow-y-scroll">
        <div class="flex flex-col gap-y-4 p-8">
            <div class="flex flex-row items-start justify-between gap-x-4" v-for="n in 10">
                <div class="h-12 w-12 bg-gray-300 flex-shrink-0">

                </div>

                <div class="flex flex-col text-sm gap-y-1">
                    <p class="font-medium">@theuser123</p>
                    <p>dolor sit amet consectetur adipiscing elit pellentesque habitant morbi tristique senectus
                        et netus et malesuada</p>
                    <Link as="button" type="button" href="">
                    <svg width="24" height="24" class="stroke-black stroke-0" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="m12.82 5.58-.82.822-.824-.824a5.375 5.375 0 1 0-7.601 7.602l7.895 7.895a.75.75 0 0 0 1.06 0l7.902-7.897a5.376 5.376 0 0 0-.001-7.599 5.38 5.38 0 0 0-7.611 0Zm6.548 6.54L12 19.485 4.635 12.12a3.875 3.875 0 1 1 5.48-5.48l1.358 1.357a.75.75 0 0 0 1.073-.012L13.88 6.64a3.88 3.88 0 0 1 5.487 5.48Z" />
                    </svg>
                    </Link>
                </div>
            </div>
        </div>

        <form class="flex p-6 flex-row gap-x-4 left-8 sticky bottom-0 z-20 bg-white border-t"
            @submit.prevent="submitComment" ref="box">
            <div class="h-10 w-10 bg-gray-300 flex-shrink-0">

            </div>
            <input type="text" name="comment" id="comment" class="h-10 flex-grow placeholder:px-2" placeholder="Comment on this post.." v-model="comment.comment">
            <button type="submit" class="border text-white bg-black px-4 h-10 text-sm w-fit">Comment!</button>
        </form>
    </div>
</template>