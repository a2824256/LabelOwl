<template>
  <div>
    <div class="canvas_div" id="canvas_div">
      <canvas id="label_canvas" class="canvas"></canvas>
    </div>
    <button id="deleteLastPoint" @click="deleteLastPoint">删除上一个点</button>
  </div>
</template>

<script>
export default {
  data () {
    return {
      canvas: null,
      ctx: null,
      x: 0,
      y: 0,
      lastX: null,
      lastY: null,
      image: null,
      name: 'LabelPage',
      startPoint: [],
      drawingLines: [],
      mouseInCanvas: false,
      canvasDiv: null,
      count: 1,
      drawSwitch: true,
      canvasArea: null,
      labelCounter: 0,
      colors: ['red', 'blue', 'green', 'yellow'],
      labelCompelete: false
    }
  },
  mounted () {
    this.initCanvas()
    window.addEventListener('click', this.mouseClick)
    this.canvasDiv = document.getElementById('canvas_div')
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
      if (this.drawSwitch === true && this.labelCompelete === false) {
        this.x = evt.offsetX
        this.y = evt.offsetY
        if (evt.path[0].className !== 'canvas') return
        if (this.x < this.canvasArea.left || this.x > (this.canvasArea.left + this.canvasArea.width)) return
        if (this.y < this.canvasArea.top || this.y > (this.canvasArea.top + this.canvasArea.height)) return
        if (Math.abs(this.x - this.startPoint[0]) <= 2 && Math.abs(this.y - this.startPoint[1]) <= 2 && this.drawingLines.length > 1) {
          this.x = this.startPoint[0]
          this.y = this.startPoint[1]
          this.labelCompelete = true
        }
        let newCanvas = document.createElement('canvas')
        newCanvas.width = this.canvas.width
        newCanvas.height = this.canvas.height
        newCanvas.id = 'canvas' + this.count
        newCanvas.className = 'canvas'
        newCanvas.style = 'position: absolute;z-index:' + this.count + ';'
        this.canvasDiv.appendChild(newCanvas)
        let ctx = newCanvas.getContext('2d')
        ctx.fillStyle = 'red'
        ctx.beginPath()
        ctx.arc(this.x, this.y, 4, 0, Math.PI * 2)
        ctx.fill()
        ctx.closePath()
        if (this.lastX != null && this.lastY != null) {
          this.drawLine(ctx, this.x, this.y, this.lastX, this.lastY)
        } else {
          this.startPoint = [this.x, this.y]
        }
        this.lastX = this.x
        this.lastY = this.y
        this.drawingLines.push([this.x, this.y])
        this.count += 1
      }
    },
    pictureDisplay () {
      this.ctx.drawImage(this.image, 0, 0, this.canvas.width, this.canvas.height)
      this.canvasArea = this.canvas.getBoundingClientRect()
    },
    drawLine (ctx, x1, y1, x2, y2) {
      ctx.save()
      ctx.beginPath()
      ctx.lineCap = 'round'
      ctx.lineJoin = 'round'
      ctx.createLinearGradient(x1, y1, x2, y2)
      ctx.moveTo(x1, y1)
      ctx.lineTo(x2, y2)
      ctx.closePath()
      ctx.strokeStyle = 'red'
      ctx.stroke()
      ctx.restore()
    },
    deleteLastPoint () {
      if (this.drawingLines.length !== 0) {
        this.drawingLines.pop()
        let delNode = document.getElementById('canvas' + (this.count - 1))
        delNode.remove()
        this.count -= 1
        this.labelCompelete = false
        if (this.drawingLines.hasOwnProperty(this.count - 2)) {
          this.lastX = this.drawingLines[this.count - 2][0]
          this.lastY = this.drawingLines[this.count - 2][1]
        } else {
          this.lastX = null
          this.lastY = null
        }
      }
    }
  }
}
</script>

<style scoped>
  .canvas_div {
    position: relative;
  }
  .canvas {
    position: absolute;
  }
  #deleteLastPoint{
    margin-top: 720px;
    /*padding-top: 800px;*/
  }
  canvas {
    position: absolute;
    background:rgba(255,255,255,0);
    z-index:1;
    top:0px;
    left:0px;
    width: 1280px;
    height: 720px;
  }
</style>
