import { Component, EventEmitter, Output } from '@angular/core';
import { LoginService } from '../_services/login.service';
import { NgForm } from '@angular/forms';
import { LoginRequest } from '../_models/Employees/LoginRequest';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent {
  model: LoginRequest = { email: "", password: "" };

  @Output() sessionInfoEvent = new EventEmitter<void>();

  constructor(private _loginService: LoginService, private router: Router) { };

  submitForm(form: NgForm) {

    if (!form.valid) {
      return alert("Form is not valid. Try again.");
    } else {
      this._loginService.authenticateLogin(this.model).subscribe(response => {
        sessionStorage.setItem('token', response.token);
        sessionStorage.setItem('user', response.user.email);
        sessionStorage.setItem('role', response.user.isAdmin ? 'admin' : 'agent');
        this.sessionInfoEvent.emit();
        this.router.navigate([sessionStorage.getItem('role')]);
      })
    }
  }
}
