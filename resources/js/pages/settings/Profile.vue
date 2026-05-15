<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Switch } from '@/components/ui/switch';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';

type Props = {
    mustVerifyEmail: boolean;
    status?: string;
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Profile settings',
                href: edit(),
            },
        ],
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head title="Profile settings" />

    <h1 class="sr-only">Profile settings</h1>

    <div class="flex flex-col space-y-6">
        <Heading
            variant="small"
            title="Profile information"
            description="Update your name, email, and public profile details."
        />

        <Form
            v-bind="ProfileController.update.form()"
            class="space-y-8"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-6 md:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        class="mt-1 block w-full"
                        name="name"
                        :default-value="user.name"
                        required
                        autocomplete="name"
                        placeholder="Full name"
                    />
                    <InputError class="mt-2" :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        name="email"
                        :default-value="user.email"
                        required
                        autocomplete="username"
                        placeholder="Email address"
                    />
                    <InputError class="mt-2" :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="age">Age</Label>
                    <Input
                        id="age"
                        type="number"
                        class="mt-1 block w-full"
                        name="age"
                        :default-value="user.age"
                        placeholder="e.g. 28"
                        min="13" max="120"
                    />
                    <InputError class="mt-2" :message="errors.age" />
                </div>

                <div class="grid gap-2">
                    <Label for="phone_number">Phone Number</Label>
                    <Input
                        id="phone_number"
                        type="tel"
                        class="mt-1 block w-full"
                        name="phone_number"
                        :default-value="user.phone_number"
                        placeholder="(123) 456-7890"
                    />
                    <InputError class="mt-2" :message="errors.phone_number" />
                </div>

                <div class="grid gap-2 md:col-span-2">
                    <Label for="address">Street Address</Label>
                    <Input
                        id="address"
                        class="mt-1 block w-full"
                        name="address"
                        :default-value="user.address"
                        placeholder="123 Main St"
                    />
                    <InputError class="mt-2" :message="errors.address" />
                </div>

                <div class="grid gap-2">
                    <Label for="city">City</Label>
                    <Input
                        id="city"
                        class="mt-1 block w-full"
                        name="city"
                        :default-value="user.city"
                        placeholder="Seattle"
                    />
                    <InputError class="mt-2" :message="errors.city" />
                </div>

                <div class="grid gap-2">
                    <Label for="zip_code">Zip Code</Label>
                    <Input
                        id="zip_code"
                        class="mt-1 block w-full"
                        name="zip_code"
                        :default-value="user.zip_code"
                        placeholder="98101"
                    />
                    <InputError class="mt-2" :message="errors.zip_code" />
                </div>
            </div>

            <Separator />

            <div class="space-y-4">
                <Heading
                    variant="small"
                    title="Privacy Settings"
                    description="Control what information is visible on your public profile. Everything is hidden by default."
                />

                <div class="space-y-4 pt-4">
                    <div class="flex items-center justify-between border rounded-lg p-4 bg-card">
                        <div class="space-y-0.5">
                            <Label>Show Age</Label>
                            <p class="text-sm text-muted-foreground">Display your age on your public profile.</p>
                        </div>
                        <input type="hidden" name="show_age" value="0" />
                        <Switch name="show_age" value="1" :default-checked="user.show_age" />
                    </div>

                    <div class="flex items-center justify-between border rounded-lg p-4 bg-card">
                        <div class="space-y-0.5">
                            <Label>Show Location</Label>
                            <p class="text-sm text-muted-foreground">Display your City and Zip Code.</p>
                        </div>
                        <input type="hidden" name="show_location" value="0" />
                        <Switch name="show_location" value="1" :default-checked="user.show_location" />
                    </div>

                    <div class="flex items-center justify-between border rounded-lg p-4 bg-card">
                        <div class="space-y-0.5">
                            <Label>Show Address</Label>
                            <p class="text-sm text-muted-foreground">Display your full street address.</p>
                        </div>
                        <input type="hidden" name="show_address" value="0" />
                        <Switch name="show_address" value="1" :default-checked="user.show_address" />
                    </div>

                    <div class="flex items-center justify-between border rounded-lg p-4 bg-card">
                        <div class="space-y-0.5">
                            <Label>Show Phone Number</Label>
                            <p class="text-sm text-muted-foreground">Allow others to see your phone number.</p>
                        </div>
                        <input type="hidden" name="show_phone" value="0" />
                        <Switch name="show_phone" value="1" :default-checked="user.show_phone" />
                    </div>

                    <div class="flex items-center justify-between border rounded-lg p-4 bg-card">
                        <div class="space-y-0.5">
                            <Label>Show Email</Label>
                            <p class="text-sm text-muted-foreground">Allow others to see your email address.</p>
                        </div>
                        <input type="hidden" name="show_email" value="0" />
                        <Switch name="show_email" value="1" :default-checked="user.show_email" />
                    </div>
                </div>
            </div>

            <div v-if="mustVerifyEmail && !user.email_verified_at">
                <p class="-mt-4 text-sm text-muted-foreground">
                    Your email address is unverified.
                    <Link
                        :href="send()"
                        as="button"
                        class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                    >
                        Click here to resend the verification email.
                    </Link>
                </p>

                <div
                    v-if="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing" data-test="update-profile-button"
                    >Save Profile Settings</Button
                >
            </div>
        </Form>
    </div>

    <DeleteUser />
</template>
