import { Component, OnInit } from '@angular/core';
import { FlightService } from './_services/flight.service';
import { AccessTokenResponse } from './_models/AccessTokenResponse';
import { environment } from 'src/environments/environment.development';
import { HotelService } from './_services/hotel.service';


@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
  title = 'NM Flights';
  accessToken: AccessTokenResponse = { access_token: "" };

  constructor(private _flightService: FlightService, private _hotelService: HotelService) { }

  ngOnInit() {
    this._flightService.getAccessToken().subscribe(accessTokenResponse => {
      environment.access_token = accessTokenResponse.access_token;

      // TODO: Make env service
      this._flightService.setHttpOptions();
      this._hotelService.setHttpOptions();
    });
  }
}
