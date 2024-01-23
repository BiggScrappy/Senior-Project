const express = require("express")
const path = require('path')
const app = express()

var PORT = process.env.port || 3000

// View Engine Setup
app.set("views", path.join(__dirname))
app.set("view engine", "ejs")

app.get("/", function(req, res){
	
	// Sample data to be filled in form
	var user = {
		email: 'test@gmail.com',
		name: 'Gourav',
		mobile: 9999999999,
		address: 'ABC Colony, House 23, India'
	}

	res.render("SampleForm",
		{
			user: user
		}
	);
})

app.listen(PORT, function(error){
	if(error) throw error
	console.log("Server created Successfully on PORT", PORT)
})


