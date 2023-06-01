import { Component } from '@angular/core';
import { LoginService } from '../_services/login.service';
import { NgForm } from '@angular/forms';
import { LoginRequest } from '../_models/Employees/LoginRequest';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent {
  model: LoginRequest = {email: "", password: ""};

  constructor(private _loginService: LoginService){};

  submitForm(form: NgForm) {

    if (!form.valid) {
      return alert("Form is not valid. Try again.");
    } else {
      this._loginService.authenticateLogin(this.model).subscribe(response => {
        
      })
    }
  }
}
