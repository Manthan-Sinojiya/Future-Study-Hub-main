function validateForm() {
    let student_name = document.forms["form"]["student_name"].value;
    let email = document.forms["form"]["email"].value;
    let dob = document.forms["form"]["dob"].value;
    let password = document.forms["form"]["password"].value;
    let contact_no = document.forms["form"]["contact_no"].value;
    let gender = document.forms["form"]["gender"].value;
    
    if (student_name == "") {
        alert("Name must be filled out");
        return false;
    }
    
    if (email == "") {
        alert("Email must be filled out");
        return false;
    }
    
    if (dob == "") {
        alert("Dob must be filled out");
        return false;
    }
    if (password == "") {
        alert("Password must be filled out");
        return false;
    }
    if (contact_no == "") {
        alert("Contact No must be filled out");
        return false;
    }
    if (gender == "") {
        alert("Gender must be filled out");
        return false;
    }
}
