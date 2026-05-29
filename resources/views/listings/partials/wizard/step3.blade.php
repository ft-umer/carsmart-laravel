<div class="space-y-6">

    <div>
        <h3 class="text-lg font-semibold text-gray-900">
            Media Uploads
        </h3>

        <p class="text-sm text-gray-500 mt-1">
            Upload all required vehicle photos and media assets.
        </p>
    </div>

    {{-- Required Photos --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">

        @php
            $requiredPhotos = [
                'Front 3/4',
                'Rear 3/4',
                'Odometer',
                'VIN Plate',
                'Engine Bay',
                'Interior'
            ];
        @endphp

        @foreach($requiredPhotos as $photo)
            <div class="border border-dashed border-gray-300 rounded-2xl p-4 bg-gray-50">

                <label class="block text-xs font-semibold mb-3 text-gray-700">
                    {{ $photo }} <span class="text-red-500">*</span>
                </label>

                <input type="file" class="kt-input w-full">

                <input
                    type="text"
                    class="kt-input w-full mt-3"
                    placeholder="Caption">

                <div class="flex items-center gap-2 mt-3">
                    <input type="radio" name="hero_photo">
                    <label class="text-xs text-gray-600">
                        Mark as hero image
                    </label>
                </div>

            </div>
        @endforeach

    </div>

    {{-- Video URL --}}
    <div>
        <label class="block text-xs font-medium mb-1">
            Video URL (Optional)
        </label>

        <input
            type="url"
            class="kt-input w-full"
            placeholder="https://youtube.com/...">
    </div>

    {{-- Reordering Note --}}
    <div class="rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
        Drag and drop ordering can be connected later without changing current upload logic.
    </div>

</div>