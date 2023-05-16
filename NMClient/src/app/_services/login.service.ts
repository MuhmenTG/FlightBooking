import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from 'src/environments/environment.development';
import { LoginRequest } from '../_models/Employees/LoginRequest';
import { Observable } from 'rxjs';
import { LoginResponse } from '../_models/Employees/LoginResponse';
import { LogoutRequest } from '../_models/Employees/LogoutRequest';

@Injectable({
  providedIn: 'root'
})
export class LoginService {
  private apiUrl = environment.apiUrl + "/auth";

  httpOptions = {
    headers: new HttpHeaders({
      'Content-Type': 'application/json'
    })
  }

  constructor(private http: HttpClient) { }

  authenticateLogin(body: LoginRequest): Observable<LoginResponse> {
    return this.http.post<LoginResponse>(this.apiUrl + "/login", JSON.stringify(body), this.httpOptions);
  }

  authenticateLogout(body: LogoutRequest): Observable<void> {
    return this.http.post<void>(this.apiUrl + "/logout", JSON.stringify(body), this.httpOptions);
  }
}
