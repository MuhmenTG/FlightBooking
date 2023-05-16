import { Component, OnInit } from '@angular/core';
import { AccessTokenResponse } from './_models/AccessTokenResponse';
import { HttpService } from './_services/http.service';


@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
  title = 'NM Flights';
  accessToken: AccessTokenResponse = { access_token: "" };

  constructor(private _httpService: HttpService) { }

  ngOnInit() {
    this._httpService.getAccessToken().subscribe(accessTokenResponse => {
      this._httpService.setHttpOptions(accessTokenResponse.access_token);
    });
  }
}