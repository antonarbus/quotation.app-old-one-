//#region INIT EXPRESS
  
  const express = require('express') 
  const multer = require('multer')
  const app = express()
  const path = require('path') 
  const Cloud = require('@google-cloud/storage')
  const { Storage } = Cloud
  const serviceKey = path.join(__dirname, './quotationapp-8014c-04cff2d88d5b.json')
  const storage = new Storage({ keyFilename: serviceKey, projectId: 'quotationapp-8014c' })
  const bucket = storage.bucket('quotation-app-bucket')
  const util = require('util')
  const { format } = util
  const crypto = require("crypto")

  const PORT = process.env.PORT || 8080
  app.set('trust proxy', true); // for app engine
  app.listen(PORT, () => console.log(`Server started on port ${PORT}`))

//#endregion

//#region SERVE STATIC FILES

  // https://expressjs.com/en/4x/api.html#express.static
  // http://localhost:8080
  app.use(express.static(path.join(__dirname, 'public'), { 
    extensions:['html'] // no need to put html extension
  }))
  // manually serve files
  //app.get('/', (req, res) => res.sendFile(path.join(__dirname, 'index.html'))) 

//#endregion


// http://localhost:8080/upload_file

const multerMid = multer({ storage: multer.memoryStorage(), limits: { fileSize: 20 * 1024 * 1024, } })
app.use(multerMid.single('file'))
// app.use('/upload_file', express.json())
// app.use('/upload_file', express.urlencoded({ extended: true }) )
app.post('/upload_file', async (req, res, next) => {
  try {
    const uploadedFile = await upload(req.file) // upload file
    const {fileName, link} = await upload(req.file) // upload file
    await bucket.file(fileName).makePublic() // make file public
    
    const fileInfo = {
      link: link,
      origName: req.file.originalname,
      size: req.file.size / 1024 / 1024,
      date: new Date()
    }
    // ACTION: send fileInfo into user's DB
    res.status(200).json({ link: link })
  } catch (error) {
    console.log(error)
    next(error)
  }
})


// https://medium.com/@olamilekan001/image-upload-with-google-cloud-storage-and-node-js-a1cf9baa1876
const upload = (file) => new Promise((resolve, reject) => {
  const fileHash = crypto.createHash("sha256").update(file.buffer).digest("hex")
  const fileExt = path.extname(file.originalname) // fileExt constrains '.' // .doc 
  const finalFileName = fileHash + fileExt 

  const blob = bucket.file(finalFileName)
  const blobStream = blob.createWriteStream({ resumable: false })

  blobStream
  .on('finish', () => {
    const publicUrl = format(`https://storage.googleapis.com/${bucket.name}/${finalFileName}`)
    resolve({ link: publicUrl, fileName: finalFileName })
  })
  .on('error', () => {
    reject(`Unable to upload file, something went wrong`)
  })
  .end(file.buffer)
})



// 404 response as the final middleware
// app.use(function (req, res, next) {
//   res.status(404).send("404: ups, can't find that!")
// })