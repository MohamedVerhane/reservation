/**
 * Filters the room type select based on the selected hotel on
 * room create/edit pages.
 *
 * The hotel `<select>` carries a `data-room-types` JSON attribute mapping
 * `hotel_id` -> [{ id, name }, ...]. Changing the hotel rebuilds the
 * `room_type_id` options so only that hotel's room types are shown.
 */
function initRoomTypeFilter() {
    document.querySelectorAll("select[data-room-types]").forEach((hotelSelect) => {
        const form = hotelSelect.closest("form");
        const typeSelect = form ? form.querySelector('select[name="room_type_id"]') : null;
        if (!typeSelect) return;

        let mapping = {};
        try {
            mapping = JSON.parse(hotelSelect.dataset.roomTypes || "{}");
        } catch (_) {
            mapping = {};
        }

        const placeholderText = typeSelect.querySelector("option[value='']")?.textContent || "";
        const emptyText = typeSelect.dataset.empty || "No room types for this hotel";

        function filter() {
            const hotelId = hotelSelect.value;
            const list = hotelId ? (mapping[hotelId] || []) : Object.values(mapping).flat();
            const current = typeSelect.value;

            typeSelect.innerHTML = "";

            const ph = document.createElement("option");
            ph.value = "";
            ph.textContent = placeholderText;
            typeSelect.appendChild(ph);

            if (list.length) {
                list.forEach((rt) => {
                    const opt = document.createElement("option");
                    opt.value = rt.id;
                    opt.textContent = rt.name;
                    typeSelect.appendChild(opt);
                });
            } else {
                const none = document.createElement("option");
                none.value = "";
                none.textContent = hotelId ? emptyText : placeholderText;
                none.disabled = true;
                typeSelect.appendChild(none);
            }

            if (current && list.some((rt) => String(rt.id) === current)) {
                typeSelect.value = current;
            }
        }

        hotelSelect.addEventListener("change", filter);
        hotelSelect.addEventListener("input", filter);
        filter();
    });
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initRoomTypeFilter);
} else {
    initRoomTypeFilter();
}
