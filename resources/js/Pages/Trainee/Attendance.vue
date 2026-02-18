<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { format } from 'date-fns';
import { CalendarIcon, ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline';
import type { AttendancePageProps } from '@/types/trainee';

const props = defineProps<AttendancePageProps>();

// Format current date
const currentDate = computed(() => format(new Date(), 'EEEE, MMMM d, yyyy'));

// Get month name
const monthName = computed(() => {
  const date = new Date(props.currentYear, props.currentMonth - 1);
  return format(date, 'MMMM yyyy');
});

/**
 * Navigate to previous month
 */
const goToPreviousMonth = () => {
  let newMonth = props.currentMonth - 1;
  let newYear = props.currentYear;

  if (newMonth < 1) {
    newMonth = 12;
    newYear--;
  }

  const params = new URLSearchParams({
    month: String(newMonth),
    year: String(newYear),
  });

  window.location.href = `/trainee/attendance?${params.toString()}`;
};

/**
 * Navigate to next month
 */
const goToNextMonth = () => {
  let newMonth = props.currentMonth + 1;
  let newYear = props.currentYear;

  if (newMonth > 12) {
    newMonth = 1;
    newYear++;
  }

  const params = new URLSearchParams({
    month: String(newMonth),
    year: String(newYear),
  });

  window.location.href = `/trainee/attendance?${params.toString()}`;
};

/**
 * Get color for status badge in table
 */
const getStatusBadgeColor = (status: string) => {
  switch (status) {
    case 'present':
      return 'bg-green-100 text-green-800';
    case 'late':
      return 'bg-yellow-100 text-yellow-800';
    case 'absent':
      return 'bg-red-100 text-red-800';
    case 'on_leave':
      return 'bg-blue-100 text-blue-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};

/**
 * Format time for display
 */
const formatTimeDisplay = (timeString: string | null): string => {
  if (!timeString) return '--:--';
  const parts = timeString.split(':');
  return `${parts[0]}:${parts[1]}`;
};
</script>

<template>
  <Head title="Attendance" />

  <AuthenticatedLayout>
    <!-- Page Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Attendance Records</h1>
      <p class="text-gray-500 mt-2">{{ currentDate }}</p>
    </div>

    <!-- Calendar Section -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 mb-8">
      <!-- Calendar Header -->
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
          <CalendarIcon class="w-5 h-5 text-indigo-600" />
          {{ monthName }}
        </h2>
        <div class="flex items-center gap-2">
          <button
            @click="goToPreviousMonth"
            class="p-2 rounded-lg hover:bg-gray-100 transition text-gray-600"
            title="Previous month"
          >
            <ChevronLeftIcon class="w-5 h-5" />
          </button>
          <button
            @click="goToNextMonth"
            class="p-2 rounded-lg hover:bg-gray-100 transition text-gray-600"
            title="Next month"
          >
            <ChevronRightIcon class="w-5 h-5" />
          </button>
        </div>
      </div>

      <!-- Calendar Grid -->
      <div class="mb-8">
        <!-- Weekdays Header -->
        <div class="grid grid-cols-7 gap-2 mb-2">
          <div v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day" class="text-center">
            <p class="font-semibold text-gray-600 text-sm">{{ day }}</p>
          </div>
        </div>

        <!-- Calendar Days -->
        <div class="grid grid-cols-7 gap-2">
          <div
            v-for="day in props.calendarDays"
            :key="day.date"
            :class="[
              'p-3 rounded-lg border-2 min-h-20 flex flex-col items-center justify-center',
              day.hasRecord ? day.statusClass : 'bg-white border-gray-200 text-gray-600',
            ]"
          >
            <p class="font-semibold text-sm">{{ day.day }}</p>
            <p v-if="day.status" class="text-xs font-medium mt-1 capitalize">
              {{ day.status.replace('_', ' ') }}
            </p>
          </div>
        </div>
      </div>

      <!-- Legend -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6 border-t border-gray-200">
        <div class="flex items-center gap-2">
          <div class="w-4 h-4 rounded bg-green-100 border-2 border-green-300" />
          <span class="text-sm text-gray-600">Present</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-4 h-4 rounded bg-yellow-100 border-2 border-yellow-300" />
          <span class="text-sm text-gray-600">Late</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-4 h-4 rounded bg-red-100 border-2 border-red-300" />
          <span class="text-sm text-gray-600">Absent</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-4 h-4 rounded bg-blue-100 border-2 border-blue-300" />
          <span class="text-sm text-gray-600">On Leave</span>
        </div>
      </div>
    </div>

    <!-- Attendance Statistics -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
      <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4">
        <p class="text-sm text-gray-600 font-medium">Total Work Days</p>
        <p class="text-2xl font-bold text-gray-900 mt-2">{{ props.statistics.totalDays }}</p>
      </div>
      <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4">
        <p class="text-sm text-gray-600 font-medium">Present</p>
        <p class="text-2xl font-bold text-green-600 mt-2">{{ props.statistics.present }}</p>
      </div>
      <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4">
        <p class="text-sm text-gray-600 font-medium">Late</p>
        <p class="text-2xl font-bold text-yellow-600 mt-2">{{ props.statistics.late }}</p>
      </div>
      <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4">
        <p class="text-sm text-gray-600 font-medium">Absent</p>
        <p class="text-2xl font-bold text-red-600 mt-2">{{ props.statistics.absent }}</p>
      </div>
      <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4">
        <p class="text-sm text-gray-600 font-medium">Attendance Rate</p>
        <p class="text-2xl font-bold text-indigo-600 mt-2">{{ props.statistics.attendanceRate }}%</p>
      </div>
    </div>

    <!-- Detailed Records Table -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Detailed Records</h2>

      <div v-if="props.detailedRecords.length === 0" class="text-center py-8">
        <p class="text-gray-500">No attendance records for this month</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-gray-700">Date</th>
              <th class="px-4 py-3 text-left font-semibold text-gray-700">Clock In</th>
              <th class="px-4 py-3 text-left font-semibold text-gray-700">Clock Out</th>
              <th class="px-4 py-3 text-left font-semibold text-gray-700">Duration</th>
              <th class="px-4 py-3 text-left font-semibold text-gray-700">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="record in props.detailedRecords"
              :key="record.id"
              class="border-b border-gray-200 hover:bg-gray-50 transition"
            >
              <td class="px-4 py-3 text-gray-900 font-medium">
                {{ format(new Date(record.date), 'MMM d, yyyy') }}
              </td>
              <td class="px-4 py-3 text-gray-600">{{ formatTimeDisplay(record.clockIn) }}</td>
              <td class="px-4 py-3 text-gray-600">{{ formatTimeDisplay(record.clockOut) }}</td>
              <td class="px-4 py-3 text-gray-600">{{ record.duration || '--:--' }}</td>
              <td class="px-4 py-3">
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-semibold',
                    getStatusBadgeColor(record.status),
                  ]"
                >
                  {{ record.statusBadge.label }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Navigation Links -->
    <div class="mt-8 flex justify-center gap-4">
      <Link
        href="/trainee/dashboard"
        class="px-6 py-3 bg-gray-200 text-gray-900 rounded-lg font-medium hover:bg-gray-300 transition"
      >
        Back to Dashboard
      </Link>
    </div>
  </AuthenticatedLayout>
</template>
