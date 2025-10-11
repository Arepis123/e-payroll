<div>
    <flux:modal name="edit-post" class="md:w-900">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Edit Post</flux:heading>
                <flux:text class="mt-2">Add details to the post. Let's add the details here.</flux:text>
            </div>

            <flux:input wire:model="title" label="Title" placeholder="Your post" />
            <flux:textarea wire:model="body" label="Body" placeholder="Your body" />

            <div class="flex">
                <flux:spacer />

                <flux:button type="submit" variant="primary" wire:click="update">Save changes</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
