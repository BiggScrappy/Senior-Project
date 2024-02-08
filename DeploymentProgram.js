const fs = require('fs');
const path = require('path');

function deployIndividualFiles(deployPath) {
    // Simulating deployment of individual files
    const filesToDeploy = ['index.html', 'style.css'];

    filesToDeploy.forEach(file => {
        const sourcePath = file;
        const destinationPath = path.join(deployPath, file);

        fs.copyFileSync(sourcePath, destinationPath);
        console.log(`Deployed ${file} to ${deployPath}`);
    });
}

function deployEntireDirectory(deployPath) {
    // Simulating deployment of an entire directory
    const sourceDirectory = 'images';
    const destinationDirectory = path.join(deployPath, 'images');

    fs.mkdirSync(destinationDirectory, { recursive: true });
    fs.readdirSync(sourceDirectory).forEach(file => {
        const sourceFilePath = path.join(sourceDirectory, file);
        const destinationFilePath = path.join(destinationDirectory, file);

        fs.copyFileSync(sourceFilePath, destinationFilePath);
    });

    console.log(`Deployed ${sourceDirectory} to ${deployPath}`);
}

// Set your deployment path (replace with your actual path)
const deployPath = '/home/example/public_html/';

// Simulate deploying individual files
deployIndividualFiles(deployPath);

// Simulate deploying an entire directory
deployEntireDirectory(deployPath);

// 2/7/24 
// The provided code examples simulate a simplified deployment process for a web application by copying files (either individual files or an entire directory) 
// to a specified deployment path. 

// Deploy Individual Files:
// The script simulates the deployment of specific files (e.g., index.html and style.css) to a target deployment directory (/home/example/public_html/).
// The shutil module in Python, fs module in JavaScript (Node.js), and FileUtils module in Ruby are used to copy files.

// Deploy an Entire Directory:
// The script simulates the deployment of an entire directory (e.g., images) and its contents to a target deployment directory (/home/example/public_html/).
// This involves copying the entire directory structure, including subdirectories and files.

// Print Deployment Information:
//The scripts print messages to the console indicating which files or directories are deployed to the specified deployment path.