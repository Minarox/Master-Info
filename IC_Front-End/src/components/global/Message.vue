<template>
  <div id="message" :class="open ? '' : 'close'">
    <article :class="!success ? 'success' : 'error'" @click="open = !open">
      <p>{{ message }}</p>
    </article>
  </div>
</template>

<script>
export default {
  name: "Message",
  props: ["success", "message"],
  data() {
    return {
      open: true,
    };
  },
};
</script>

<style scoped>
#message {
  width: 100%;
  max-width: 700px;
  margin: 0 auto;
  position: absolute;
  bottom: -60px;
  left: 0;
  right: 0;
  animation: 5s slide alternate;
}

#message article {
  padding: 16px;
  border: 1px solid transparent;
  border-radius: 6px;
  cursor: pointer;
}

.success {
  background-color: #baeaacff;
  border-color: #99c098ff !important;
}

.success p {
  color: #0f5132;
}

.error {
  background-color: #f1c1c1ff;
  border-color: #c09898ff !important;
}

.error p {
  color: #842029;
}

.close {
  bottom: -60px !important;
  transition: all 0.3s ease-in-out;
}

@keyframes slide {
  0% {
    bottom: -60px;
  }
  15% {
    bottom: 20px;
  }
  85% {
    bottom: 20px;
  }
  100% {
    bottom: -60px;
  }
}
</style>
