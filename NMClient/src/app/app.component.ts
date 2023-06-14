import { Component, OnInit } from '@angular/core';
import { AccessTokenResponse } from './_models/AccessTokenResponse';
import { HttpService } from './_services/http.service';
import { Router } from '@angular/router';


@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
  title = 'NM Flights';
  accessToken: AccessTokenResponse = { access_token: "" };
  accessTokenSet: boolean = false;
  sessionUsername: any = null;
  sessionRole: any = null;
  sessionToken: any = null;

  constructor(private _httpService: HttpService, private router: Router) { }

  ngOnInit() {
    this.sessionUsername = sessionStorage?.getItem('user');
    this.sessionRole = sessionStorage?.getItem('role');
    this.sessionToken = sessionStorage?.getItem('token');

    this._httpService.getAccessToken().subscribe(accessTokenResponse => {
      this._httpService.setHttpOptions(accessTokenResponse.access_token);
      this.accessTokenSet = this._httpService.isAccessToken();
    });
  }

  modifySessionInfo(): void {
    this.sessionUsername = sessionStorage.getItem('user');
    this.sessionRole = sessionStorage.getItem('role');
    this.sessionToken = sessionStorage.getItem('token');
  }

  logout(): void {
    sessionStorage.clear();
    this.sessionUsername = null;
    this.sessionRole = null;
    this.sessionToken = null;
    this.router.navigate(['']);
  }
}