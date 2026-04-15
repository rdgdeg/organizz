import { ref, watch, computed } from 'vue';

/**
 * Planning par jour pour les événements sur plusieurs dates.
 * @param {{ date_start: string, date_end: string, daily_window_start: string, daily_window_end: string }} form
 */
export function useDaySchedules(form, initialDaySchedules = null) {
    const isMultiDay = computed(() => {
        if (!form.date_start || !form.date_end) {
            return false;
        }

        return form.date_start < form.date_end;
    });

    const usePerDay = ref(
        Array.isArray(initialDaySchedules) && initialDaySchedules.length > 0,
    );

    const daySchedules = ref(
        Array.isArray(initialDaySchedules) && initialDaySchedules.length > 0
            ? initialDaySchedules.map((r) => ({
                  date: r.date,
                  enabled: r.enabled !== false,
                  window_start: (r.window_start || '08:00').slice(0, 5),
                  window_end: (r.window_end || '20:00').slice(0, 5),
              }))
            : [],
    );

    function rebuildFromDefaults() {
        if (!form.date_start || !form.date_end) {
            daySchedules.value = [];

            return;
        }
        const start = new Date(`${form.date_start}T12:00:00`);
        const end = new Date(`${form.date_end}T12:00:00`);
        const existing = Object.fromEntries(daySchedules.value.map((r) => [r.date, r]));
        const rows = [];
        for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
            const iso = d.toISOString().slice(0, 10);
            const prev = existing[iso];
            rows.push({
                date: iso,
                enabled: prev ? prev.enabled : true,
                window_start: prev?.window_start ?? form.daily_window_start ?? '08:00',
                window_end: prev?.window_end ?? form.daily_window_end ?? '20:00',
            });
        }
        daySchedules.value = rows;
    }

    watch(usePerDay, (v) => {
        if (v && isMultiDay.value) {
            rebuildFromDefaults();
        }
    });

    watch(
        [isMultiDay, () => form.date_start, () => form.date_end],
        () => {
            if (!isMultiDay.value) {
                usePerDay.value = false;
            }
            if (usePerDay.value && isMultiDay.value) {
                rebuildFromDefaults();
            }
        },
        { immediate: true },
    );

    watch(
        [() => form.daily_window_start, () => form.daily_window_end],
        () => {
            if (usePerDay.value && isMultiDay.value && daySchedules.value.length) {
                rebuildFromDefaults();
            }
        },
    );

    function appendToFormPayload(data) {
        const perDay = usePerDay.value && isMultiDay.value;

        return {
            ...data,
            use_per_day_schedule: perDay,
            day_schedules: perDay ? daySchedules.value : null,
        };
    }

    return {
        usePerDay,
        daySchedules,
        isMultiDay,
        rebuildFromDefaults,
        appendToFormPayload,
    };
}
