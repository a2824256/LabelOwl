<template>
  <div class="canvas_div">
    <canvas id="label_canvas"></canvas>
    <button @click="deleteLastPoint">删除上一个点</button>
  </div>
</template>

<script>
export default {
  canvas: null,
  ctx: null,
  x: 0,
  y: 0,
  lastX: null,
  lastY: null,
  image: null,
  drawingLines: [],
  name: 'LabelPage',
  startPoint: null,
  mounted () {
    this.initCanvas()
    window.addEventListener('click', this.mouseClick)
  },
  methods: {
    initCanvas () {
      this.canvas = document.getElementById('label_canvas')
      this.ctx = this.canvas.getContext('2d')
      // 必须设置和css是两样东西
      this.canvas.width = 1280
      this.canvas.height = 720
      this.image = new Image()
      this.image.src = 'http://localhost:9090/static/223.jpg'
      this.image.onload = this.pictureDisplay
    },
    mouseClick (evt) {
      this.x = evt.offsetX
      this.y = evt.offsetY
      this.ctx.fillStyle = 'red'
      this.ctx.beginPath()
      this.ctx.arc(this.x, this.y, 4, 0, Math.PI * 2)
      this.ctx.fill()
      this.ctx.closePath()
      if (this.lastX != null && this.lastY != null) {
        this.drawLine(this.x, this.y, this.lastX, this.lastY)
      }
      this.lastX = this.x
      this.lastY = this.y
      console.log(this.x + ',' + this.y)
      console.log(this.drawingLines)
      // this.drawingLines.push([this.x, this.y])
      console.log(this.drawingLines)
    },
    pictureDisplay () {
      this.ctx.drawImage(this.image, 0, 0, this.canvas.width, this.canvas.height)
    },
    drawLine (x1, y1, x2, y2) {
      this.ctx.save()
      this.ctx.beginPath()
      this.ctx.lineCap = 'round'
      this.ctx.lineJoin = 'round'
      this.ctx.createLinearGradient(x1, y1, x2, y2)
      this.ctx.moveTo(x1, y1)
      this.ctx.lineTo(x2, y2)
      this.ctx.closePath()
      this.ctx.strokeStyle = 'red'
      this.ctx.stroke()
      this.ctx.restore()
    },
    deleteLastPoint () {
      if (this.drawingLines !== []) {
        this.drawingLines.pop()
      }
    }
  }
}
</script>

<style scoped>
  #label_canvas {
    width: 1280px;
    height: 720px;
    background: black;
  }
</style>
