<script setup>
import { Container, HH1 } from "@hjbdev/ui";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineOptions({ layout: PublicLayout });

defineProps({
    upcomingEvents: Array,
});
</script>
<template>
    <Head title="UKCSTV: Counter-Strike Coverage for the UK & Ireland"></Head>
    <Container>
        <HH1 class="mb-6">Welcome to B-Site</HH1>
        <div class="prose prose-zinc dark:prose-invert">
            <p>
                The UK scene currently lacks a live match coverage site such as
                dust2.dk or VaKarM.net. Despite great work by UKCSGO with their
                event coverage and Mischief with his database, we still have a
                gap that needs to be filled.
            </p>
            <p>
                If you want to find upcoming UKCS matches, you have to trawl
                FACEIT for fixtures, look through TO websites or hope that you
                see the right tweets.
            </p>
            <p>
                Unless there's an official stream, there's no way to follow the
                scores of many of these games in real time, no HLTV scorebot.
            </p>
            <h2>Searching for a solution</h2>
            <p>
                I tried this once before with UKCS.gg. The site had good
                foundations, but ultimately the scope was too broad. I wanted to
                do both match coverage and news, and I didn't have the time to
                do either properly. With UKCSGO rising from the ashes, I decided
                to take a step back from the project.
            </p>
            <p>
                With CS2 being released, I hope B-Site can help to bolster
                coverage for the scene for years to come.
            </p>
            <h2>Tournaments</h2>
            <p>
                B-Site won't work without tournament operators getting on
                board. I've reached out to a couple of TOs already, and I'm
                hoping to have more news to share soon.
            </p>
            <p>
                The B-Site bot works the same way HLTV's bot does, using the
                <code>logaddress_add_http</code> command native to CS.
            </p>
            <h2>Sustainability and the Future</h2>
            <p>
                I've had conversations with people at LAN about how it would be
                difficult to make a site like this sustainable for the long
                term. I'm weighing up a few options currently, more on that
                soon.
            </p>
            <p>
                If you'd like to contribute to the project, you can donate to my
                <a href="https://ko-fi.com/indexgg" target="_blank"
                    >Ko-fi page</a
                >.
            </p>
            <!-- <p>
                If you're a developer interested in helping out, please get in
                touch with me on X (<a href="https://twitter.com/indexCS" target="_blank">@indexCS</a>) or Discord (indexgg). The stack is
                as follows:
            </p>
            <ul>
                <li>Laravel 10</li>
                <li>PHP8.1 + Swoole</li>
                <li>VueJS</li>
                <li>Redis (Laravel Horizon)</li>
                <li>MYSQL 8</li>
            </ul> -->
        </div>
    </Container>
</template>
