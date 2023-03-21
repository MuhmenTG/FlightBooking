import { Component, OnInit } from '@angular/core';
import { FlightService } from './_services/flight.service';
import { AccessTokenResponse } from './_models/accessTokenResponse';


@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit{
  title = 'NM Flights';
  accessToken: AccessTokenResponse = { access_token: ""};

  constructor(private _flightService: FlightService) {}

  ngOnInit(): void{
    this._flightService.getAccessToken();
    this._flightService.getFlights().subscribe(x => {console.log(x)});
  }
}
