import { Component, OnInit } from '@angular/core';
import { FlightService } from './_services/flight.service';
import { AccessTokenResponse } from './_models/AccessTokenResponse';
import { environment } from 'src/environments/environment.development';
import { SearchFlightsResponses } from './_models/SearchFlightsResponses';
import { FlightResponse } from './_models/FlightResponse';


@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
  title = 'NM Flights';
  accessToken: AccessTokenResponse = { access_token: "" };
  searchFlightsResponses: FlightResponse[] = [];

  constructor(private _flightService: FlightService) { }

  ngOnInit() {
    this._flightService.getAccessToken().subscribe(accessTokenResponse => {
      environment.access_token = accessTokenResponse.access_token;
      this._flightService.setHttpOptions();
    });
  }
}
