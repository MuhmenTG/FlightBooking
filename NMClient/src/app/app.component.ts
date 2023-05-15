import { Component, OnInit } from '@angular/core';
import { FlightService } from './_services/flight.service';
import { AccessTokenResponse } from './_models/AccessTokenResponse';
import { environment } from 'src/environments/environment.development';
import { HotelService } from './_services/hotel.service';
import { EnvironmentService } from './_services/environment.service';


@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
  title = 'NM Flights';
  accessToken: AccessTokenResponse = { access_token: "" };

  constructor(private _envService: EnvironmentService) { }

  ngOnInit() {
    this._envService.getAccessToken().subscribe(accessTokenResponse => {
      this._envService.setHttpOptions(accessTokenResponse.access_token);
    });
  }
}