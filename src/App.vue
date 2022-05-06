<template>
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

main {
  height: calc(100vh - 64px);
  overflow: auto; /* TODO: remove at the end */
}

main > div {
  width: 100%;
  max-width: 1200px;
  margin: 30px auto;
  padding: 0 12px;
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
form select {
  display: block;
  width: 100%;
  outline: none;
  padding: 8px;
  height: 36px;
  border: 1px solid var(--text-label);
  border-radius: 8px;
  margin-bottom: 26px;
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
  max-height: calc(100vh - 140px);
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

#filter form input {
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
  justify-content: space-between;
}

#main table tbody td:last-of-type > * {
  display: inline-block;
  object-fit: cover;
  width: 22px;
  height: 22px;
  margin-right: 6px;
  fill: var(--title);
}

#main table tbody tr td[colspan='4'] {
  text-align: center;
  background-color: var(--bg-content);
  padding: calc((100vh / 2) - 64px - 30px * 2) 0;
}

#main table tbody tr:nth-of-type(odd) {
  background-color: rgba(0, 0, 0, .05);
}

#buttons {
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: space-evenly;
  gap: 30px;
}

#buttons > * {
  flex: 0 0 30%;
  box-shadow: 0 3.2px 7.2px 0 rgb(0 0 0 / 13%),
  0 0.6px 1.8px 0 rgb(0 0 0 / 11%);
}
</style>