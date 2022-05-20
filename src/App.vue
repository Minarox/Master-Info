<template>
  <NotificationGroup group="success">
    <div
        class="fixed top-10 inset-0 flex items-start justify-end p-6 px-4 py-6 pointer-events-none notification"
    >
      <div class="w-full max-w-sm">
        <Notification
            v-slot="{ notifications }"
            enter="transform ease-out duration-300 transition"
            enter-from="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
            enter-to="translate-y-0 opacity-100 sm:translate-x-0"
            leave="transition ease-in duration-500"
            leave-from="opacity-100"
            leave-to="opacity-0"
            move="transition duration-500"
            move-delay="delay-300"
        >
          <div
              class="flex w-full max-w-sm mx-auto mt-4 overflow-hidden bg-white rounded-lg shadow-md"
              v-for="notification in notifications"
              :key="notification.id"
          >
            <div class="flex items-center justify-center w-12 bg-green-500">
              <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM16.6667 28.3333L8.33337 20L10.6834 17.65L16.6667 23.6166L29.3167 10.9666L31.6667 13.3333L16.6667 28.3333Z" />
              </svg>
            </div>

            <div class="px-4 py-2 -mx-3">
              <div class="mx-3">
                <span class="font-semibold text-green-500">{{ notification.title }}</span>
                <p class="text-sm text-gray-600">{{ notification.text }}</p>
              </div>
            </div>
          </div>
        </Notification>
      </div>
    </div>
  </NotificationGroup>

  <NotificationGroup group="error">
    <div
        class="fixed top-10 inset-0 flex items-start justify-end p-6 px-4 py-6 pointer-events-none notification"
    >
      <div class="w-full max-w-sm">
        <Notification
            v-slot="{ notifications }"
            enter="transform ease-out duration-300 transition"
            enter-from="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
            enter-to="translate-y-0 opacity-100 sm:translate-x-0"
            leave="transition ease-in duration-500"
            leave-from="opacity-100"
            leave-to="opacity-0"
            move="transition duration-500"
            move-delay="delay-300"
        >
          <div
              class="flex w-full max-w-sm mx-auto mt-4 overflow-hidden bg-white rounded-lg shadow-md"
              v-for="notification in notifications"
              :key="notification.id"
          >
            <div class="flex items-center justify-center w-12 bg-red-500">
              <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 3.36667C10.8167 3.36667 3.3667 10.8167 3.3667 20C3.3667 29.1833 10.8167 36.6333 20 36.6333C29.1834 36.6333 36.6334 29.1833 36.6334 20C36.6334 10.8167 29.1834 3.36667 20 3.36667ZM19.1334 33.3333V22.9H13.3334L21.6667 6.66667V17.1H27.25L19.1334 33.3333Z"/>
              </svg>
            </div>

            <div class="px-4 py-2 -mx-3">
              <div class="mx-3">
                <span class="font-semibold text-red-500">{{ notification.title }}</span>
                <p class="text-sm text-gray-600">{{ notification.text }}</p>
              </div>
            </div>
          </div>
        </Notification>
      </div>
    </div>
  </NotificationGroup>

  <router-view />
</template>

<style>
@import url("https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap");

:root {
  --title: var(--bg-color-semi-right);
  --text: #2f2f2f;
  --text-label: #565656;

  --bg-color-left: #2c5364;
  --bg-color-semi-left: #264755;
  --bg-color-center: #203a43;
  --bg-color-semi-right: #172C34;
  --bg-color-right: #0f2027;

  --bg-content: white;
  --bg-content-header: rgba(15, 32, 39, 0.12);
  --scroll-bar: var(--bg-color-semi-right);

  --btn: #2c5364;
  --btn-hover: #427185;

  --btn-warning: #bc4747;
  --btn-warning-hover: #e05353;

  --btn-back: #575757;
  --btn-back-hover: #797979;
}

::-webkit-scrollbar {
  width: 14px;
}

::-webkit-scrollbar-track {
  box-shadow: inset 0 0 14px 14px transparent;
  border: solid 4px transparent;
}

::-webkit-scrollbar-thumb {
  box-shadow: inset 0 0 14px 14px var(--scroll-bar);
  border: solid 4px transparent;
  border-radius: 14px;
}

::-webkit-scrollbar-button {
  display: none;
}

* {
  margin: 0;
  padding: 0;
  font: inherit;
  color: var(--text);
  box-sizing: border-box;
}

body {
  margin: 0;
  padding: 0;
  background: var(--bg-color-center);
  background: linear-gradient(to right, var(--bg-color-left), var(--bg-color-center), var(--bg-color-right));
  font-family: "Roboto", "Helvetica", "Arial", sans-serif;
  user-select: none;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  scroll-behavior: smooth;
  overflow: hidden;
}

main > div {
  width: 100%;
  max-width: 1200px;
  margin: 30px auto;
  padding: 0 30px;
  display: flex;
  flex-flow: column nowrap;
  gap: 30px;
}

main div > article {
  flex: 1 0 0 !important;
}

form label {
  display: block;
  margin: 0 0 6px 4px;
  color: var(--text-label);
}

form input,
form select,
form textarea {
  display: block;
  width: 100%;
  outline: none;
  padding: 8px;
  height: 36px;
  border: 1px solid var(--text-label);
  border-radius: 8px;
  margin-bottom: 26px;
}

form textarea {
  height: 250px;
  resize: vertical;
}

form button,
.button {
  display: block;
  margin: 0 auto;
  outline: none;
  cursor: pointer;
  border-radius: 8px;
  font-weight: 500;
  line-height: 16px;
  padding: 10px 24px;
  border: none;
  color: white;
  background-color: var(--btn);
  transition: background-color 0.17s ease, color 0.17s ease;
  text-decoration: none;
  text-align: center;
}

.btn-warning {
  background-color: var(--btn-warning) !important;
}

.btn-back {
  background-color: var(--btn-back) !important;
}

form button:hover,
.button:hover {
  background-color: var(--btn-hover);
}

.btn-warning:hover {
  background-color: var(--btn-warning-hover) !important;
}

.btn-back:hover {
  background-color: var(--btn-back-hover) !important;
}

form button > * {
  color: white;
}

.popup {
  z-index: 1;
  padding: 12px;
  position: absolute;
  top: 64px;
  width: 100vw;
  height: calc(100vh - 64px);
  background-color: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: center;
  animation: fade-in 0.4s alternate;
}

@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.popup article {
  width: 100%;
  max-width: 600px;
  height: max-content;
  margin: 0 auto;
  border-radius: 12px;
  background-color: var(--bg-content);
  box-shadow: rgba(149, 157, 165, 0.2) 0 8px 24px 0;
}

.popup article > header {
  background-color: var(--bg-content-header);
  padding: 26px 12px;
  text-align: center;
  border-radius: 12px 12px 0 0;
  font-size: 1.5em;
  font-weight: 600;
}

.popup article > header > * {
  color: var(--title);
}

.popup article > section {
  padding: 20px;
  overflow: auto;
  max-height: calc(100vh - 169px);
}

.popup article > section form div {
  margin-top: 20px;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: space-evenly;
  gap: 12px 40px;
}

.popup article > section form div button {
  flex: 0 1 30%;
  margin: 0;
}

@media only screen and (max-width: 500px) {
  .popup article section form div {
    flex-flow: column nowrap;
    justify-content: center;
  }

  .popup article section form div button {
    width: 100%;
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}

.notification {
  z-index: 100 !important;
}

.svg-button {
  margin: 0;
  width: 36px;
  height: 36px;
  padding: 7px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: white;
  border-radius: 8px;
  transition: all .2s;
  box-shadow: 0 3.2px 7.2px 0 rgb(0 0 0 / 13%),
  0 0.6px 1.8px 0 rgb(0 0 0 / 11%);
  fill: var(--title);
}

.svg-button > * {
  height: calc(36px - 7px * 2);
}

.svg-button:hover {
  background-color: #dedede;
}

#filter form {
  display: flex;
  flex-flow: row nowrap;
  align-items: end;
  justify-content: space-between;
  gap: 20px;
}

#filter form div {
  flex: 1 0 0;
}

#filter form label {
  color: var(--bg-content);
}

#filter form input, #filter form select {
  margin-bottom: 0;
  border: none;
  box-shadow: 0 3.2px 7.2px 0 rgb(0 0 0 / 13%),
  0 0.6px 1.8px 0 rgb(0 0 0 / 11%);
}

#main {
  background-color: var(--bg-content);
  border-radius: 8px;
  padding: 16px;
  box-shadow: 0 3.2px 7.2px 0 rgb(0 0 0 / 13%),
  0 0.6px 1.8px 0 rgb(0 0 0 / 11%);
}

#main header {
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}

#main header h2 {
  font-size: 1.3em;
  font-weight: 500;
  color: var(--title);
}

#main header div {
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

#main header div a {
  box-shadow: inset 0 3.2px 7.2px 0 rgb(0 0 0 / 13%),
  inset 0 0.6px 1.8px 0 rgb(0 0 0 / 11%);
  fill: var(--title);
}

#main header div a:hover {
  box-shadow: inset 0 3.2px 7.2px 0 rgb(0 0 0 / 13%),
  inset 0 0.6px 1.8px 0 rgb(0 0 0 / 11%);
}

#main section {
  border-radius: 8px !important;
  box-shadow: 0 3.2px 7.2px 0 rgb(0 0 0 / 13%),
  0 0.6px 1.8px 0 rgb(0 0 0 / 11%);
  overflow: auto;
}

#main table {
  width: 100%;
  border-spacing: 0;
}

#main table thead tr {
  position: sticky;
  top: 0;
  background-color: var(--bg-content);
  box-shadow: 0 3.2px 7.2px 0 rgb(0 0 0 / 13%),
  0 0.6px 1.8px 0 rgb(0 0 0 / 11%);
}

#main table thead th {
  padding: 10px;
  text-align: left;
  background-color: var(--bg-color-center);
  color: white;
  font-size: 1.1em;
}

#main table thead th:first-of-type {
  border-radius: 8px 0 0 0;
}

#main table thead th:last-of-type {
  border-radius: 0 8px 0 0;
}

#main table tbody td {
  padding: 8px;
}

#main table tbody td:last-of-type {
  display: flex;
  align-items: center;
  gap: 12px;
}

#main table tbody td:last-of-type > * {
  display: inline-block;
  object-fit: cover;
  width: 22px;
  height: 22px;
  margin-right: 6px;
  fill: var(--title);
}

.empty-table {
  display: table-cell !important;
  text-align: center;
  background-color: var(--bg-content);
  height: calc(100vh - 340px);
  font-size: 1.1em;
}

#main table tbody tr:nth-of-type(odd) {
  background-color: rgba(0, 0, 0, .05);
}

#buttons {
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: space-evenly;
  gap: 20px 30px;
}

#buttons > * {
  flex: 0 0 30%;
  box-shadow: 0 3.2px 7.2px 0 rgb(0 0 0 / 13%),
  0 0.6px 1.8px 0 rgb(0 0 0 / 11%);
}

@media only screen and (max-width: 700px) {
  main {
    overflow: auto;
  }

  #buttons, #filter form {
    flex-flow: column nowrap;
  }

  #buttons, #filter form div {
    flex: 1 0 100%;
  }

  #buttons .button, #filter form > * {
    width: 100%;
  }
}
</style>